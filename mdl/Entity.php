<?php

namespace dokuwiki\plugin\bez\mdl;
/*
 * All fields are stored in object as strings.
 * NULLs are converted to empty string.
 * If any attribute in object === NULL -> it means that it was not initialized
 * But we always inserts NULLs instead of empty strings.
 * https://stackoverflow.com/questions/1267999/mysql-better-to-insert-null-or-empty-string
 **/

use dokuwiki\plugin\bez\meta\PermissionDeniedException;
use dokuwiki\plugin\bez\meta\ValidationException;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

abstract class Entity {

    /** @var  Model */
    protected $model;

    /** @var Validator */
    protected $validator;

    /** @var Acl */
    protected $acl;

	abstract public static function get_columns();

	public static function get_select_columns() {
        $class = get_called_class();
	    return $class::get_columns();
    }

    public static function get_acl_columns() {
        $class = get_called_class();
        return $class::get_select_columns();
    }

	public function get_assoc($filter=NULL) {
		$assoc = array();

        $columns = $this->get_select_columns();
        if ($filter !== NULL) {
            $columns = array_intersect($columns, $filter);
        }

		foreach ($columns as $col) {
			$assoc[$col] = $this->$col;
		}
		return $assoc;
	}

    public function get_table_name() {
        $class = (new \ReflectionClass($this))->getShortName();
		return lcfirst($class);
	}

	public function __get($property) {
        if (!property_exists($this, $property) || !in_array($property, $this->get_select_columns())) {
            throw new \Exception('there is no column: "'.$property. '"" in table: "' . $this->get_table_name() . '"');
        }

        if ($this->acl_of($property) < BEZ_PERMISSION_VIEW) {
            throw new PermissionDeniedException();
        }

        return $this->$property;

	}

    protected function set_property($property, $value) {
        if ($this->acl_of($property) < BEZ_PERMISSION_CHANGE) {
            throw new PermissionDeniedException("cannot change field $property");
        }
        $this->$property = $value;
    }

    protected function set_property_array($array) {
        foreach ($array as $k => $v) {
            $this->set_property($k, $v);
        }
    }

    public function set_data($post) {
        $val_data = $this->validator->validate($post);
		if ($val_data === false) {
			throw new ValidationException($this->get_table_name(), $this->validator->get_errors());
		}

		$this->set_property_array($val_data);
    }


    public function purge() {
	    if (property_exists($this, 'content') && property_exists($this, 'content_html')) {
            $rule = $this->validator->get_rule('content');

            $html = p_render('xhtml',p_get_instructions($this->content), $ignore);

            //probably content contains only white spaces
            if (empty($html) && $rule[1] == 'NOT NULL') {
                $html = '<p></p>';
            }
            $this->content_html = $html;
        }

    }

    public function changable_fields($filter=NULL) {
       $fields = $this->acl->get_list();

       if ($filter !== NULL) {
           $fields = array_filter($fields, function ($k) use ($filter) {
                return in_array($k, $filter);
           }, ARRAY_FILTER_USE_KEY);
       }

       return array_keys(array_filter($fields, function ($var) {
           return $var >= BEZ_PERMISSION_CHANGE;
       }));
    }

    public function can_be_null($field) {
	    $rule = $this->validator->get_rule($field);
	    $null = $rule[1];
	    if (strtolower($null) == 'null') {
	        return true;
        }

        return false;
    }

    public function __construct($model) {
        $this->model = $model;
        $this->validator = new Validator($this->model);

        $this->acl = new Acl($this->model->get_level(), $this->get_acl_columns());
    }

    public function acl_of($field) {
        return $this->acl->acl_of($field);
    }

    protected function html_link_url() {
	    return '#';
    }

    protected function html_link_content() {
	    echo $this->id;
    }

    public function html_link($pre='', $post='', $print=true) {
        $ret = '<a href="'.$this->html_link_url().'">';
        $ret .= $pre . $this->html_link_content() . $post;
        $ret .= '</a>';

        if ($print) {
            echo $ret;
        }
        return $ret;
	}

	protected function getMailSubject() {
	    global $conf;
        return $conf['title'];
    }

    //http://data.agaric.com/capture-all-sent-mail-locally-postfix
    //https://askubuntu.com/questions/192572/how-do-i-read-local-email-in-thunderbird
	public function mail_notify($content, $users=false, $attachedImages=array()) {
        global $conf;

        $mailer = new PHPMailer(true);
        $mailer->CharSet = 'utf-8';
        $mailer->isHTML(true);

        if (!empty($conf['mailfrom'])) {
            if (preg_match('/(.*?)\s*<(.*?)>/', $conf['mailfrom'], $matches)) {
                $address = $matches[2];
                $name = $matches[1];
            } else {
                $address = $conf['mailfrom'];
                $name = '';
            }
            $mailer->setFrom($address, $name);
            $mailer->addReplyTo($address, $name);
        }

        $mailer->Subject = $this->getMailSubject();

        foreach ($attachedImages as $img) {
            $mailer->AddEmbeddedImage($img['path'], $img['cid']);
        }

        if ($users == FALSE) {
            $users = $this->get_participants('subscribent');

            //don't notify myself
            unset($users[$this->model->user_nick]);
        }

        $muted_users = $this->model->factory('subscription')->getMutedUsers();
        foreach ($users as $user) {
            if (is_array($user)) {
                $user = $user['user_id'];
            }
            //omit muted users
            if (in_array($user, $muted_users)) continue;

            $email = $this->model->userFactory->get_user_email($user);
            //do we have user email address
            if (!$email) continue;

            $name = $this->model->userFactory->get_user_full_name($user);

            $mailer->addAddress($email, $name);

            $token = $this->model->factory('subscription')->getUserToken($user);
            $resign_link = $this->model->action->url('unsubscribe', array('GET' => array( 't' => $token)));
            $mailer->Body = str_replace('%%resign_link%%', $resign_link, $content);

            try {
                $mailer->send();
            } catch (Exception $e) {
                $msg = $this->get_table_name() . '#' . $this->id . ': ' . $e->getMessage();
                throw new \Exception($msg);
            }

            $mailer->clearAddresses();
            $mailer->clearCustomHeaders();
        }
    }

}
