<?php

namespace dokuwiki\plugin\bez\mdl;


use dokuwiki\plugin\bez\meta\PermissionDeniedException;

class SubscriptionFactory extends Factory {

    protected function checkToken($token) {
        $q = "SELECT user_id FROM {$this->get_table_name()} WHERE token=?";
        $r = $this->model->sqlite->query($q, $token);
        return $this->model->sqlite->res2single($r);
    }

    public function mute($token='') {
        //mute by token
        if ($token) {
            $user_id = $this->checkToken($token);
            if (!$user_id) {
                throw new PermissionDeniedException();
            }
            $q = "UPDATE {$this->get_table_name()} SET mute=1 WHERE user_id=?";
            $this->model->sqlite->query($q, $user_id);
        //mute currently loged user
        } else {
            if ($this->model->get_level() < BEZ_AUTH_USER) {
                throw new PermissionDeniedException();
            }
            $q = "UPDATE {$this->get_table_name()} SET mute=1 WHERE user_id=?";
            $this->model->sqlite->query($q, $this->model->user_nick);
        }

    }

    public function unmute() {
        if ($this->model->get_level() < BEZ_AUTH_USER) {
            throw new PermissionDeniedException();
        }
        $q = "UPDATE {$this->get_table_name()} SET mute=0 WHERE user_id=?";
        $this->model->sqlite->query($q, $this->model->user_nick);
    }

    public function isMuted() {
        if ($this->model->get_level() < BEZ_AUTH_USER) {
            throw new PermissionDeniedException();
        }

        $q = "SELECT mute FROM {$this->get_table_name()} WHERE user_id=?";
        $r = $this->model->sqlite->query($q, $this->model->user_nick);
        $mute = $this->model->sqlite->res2single($r);
        if ($mute === false) {
            $this->getUserToken();
            return $this->isMuted();
        }
        return $mute;
    }

    public function getUserToken($user='') {
        if ($this->model->get_level() < BEZ_AUTH_USER) {
            throw new PermissionDeniedException();
        }
        if (!$user) $user = $this->model->user_nick;

        $q = "SELECT token FROM {$this->get_table_name()} WHERE user_id=?";
        $r = $this->model->sqlite->query($q, $user);
        $token = $this->model->sqlite->res2single($r);
        if ($token === false) {
            $token = bin2hex(openssl_random_pseudo_bytes(16));
            $this->model->sqlite->storeEntry($this->get_table_name(),
                array(  'user_id'         => $user,
                        'token'           => $token
                ));
        }
        return $token;
    }

    public function getMutedUsers() {
        $q = "SELECT user_id FROM {$this->get_table_name()} WHERE mute=1";
        $r = $this->model->sqlite->query($q);
        return array_map(function ($row) {
            return $row['user_id'];
        }, $this->model->sqlite->res2arr($r));
    }
}