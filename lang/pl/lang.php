<?php
$lang['bez'] = 'Baza Elimnacji Zagrożeń';
$lang['bez_short'] = 'BEZ';
$lang['bds_timeline'] = 'Historia';
$lang['bds_issues'] = 'Problemy';
$lang['bds_issue_report'] = 'Zgłoś problem';
$lang['report'] = 'Raport zamkniętych';
$lang['report_open'] = 'Raport otwartych';
$lang['bez_tasks'] = 'Zadania';


$lang['issues'] = 'Problemy';
$lang['tasks'] = 'Zadania';
$lang['reports'] = 'Wykazy';

$lang['issue'] = 'Problem';
$lang['task'] = 'Zadanie';


$lang['report_issue'] = 'Zgłoś problem';
$lang['id'] = 'Nr';
$lang['_id'] = 'Nr';
$lang['type'] = 'Typ problemu';
$lang['just_type'] = 'Typ';
$lang['title'] = 'Tytuł';
$lang['state'] = 'Status';
$lang['reporter'] = 'Zgłaszający';
$lang['executor'] = 'Wykonawca';
$lang['coordinator'] = 'Koordynator';
$lang['description'] = 'Opis';
$lang['date'] = 'Zgłoszone';
$lang['last_mod_date'] = 'Ostatnia zmiana';
$lang['opened_for'] = 'Otwarte od';
$lang['last_modified'] = 'Ostatnia zmieniona';
$lang['last_modified_by'] = 'Ostatnio zmieniony przez';
$lang['opened_tasks'] = 'Zadania otwarte';

$lang['entity'] = 'Źródło zgłoszenia';
$lang['entities'] = 'Źródła zgłoszeń(bez polskich znaków; jedna linia jedno źródło zgłoszenia)';
$lang['entities_confirm'] = 'Zapisano nowe źródła zgłoszeń.';

$lang['opinion'] = 'Ocena skuteczności';
$lang['root_cause'] = 'Kategoria przyczyny';


$lang['save'] = 'Zapisz';
$lang['proposal'] = 'propozycja';
$lang['reported_by'] = 'zgłoszona przez';
$lang['executor_not_specified'] = 'nie przypisany';
$lang['account_removed'] = 'konto usunięte';
$lang['none'] = 'brak';

$lang['changes_history'] = 'Historia zmian';
$lang['add_comment'] = 'Dodaj komentarz';
$lang['add_task'] = 'Dodaj zadanie';
$lang['change_issue'] = 'Zmień zgłoszenie problemu';

$lang['changed'] = 'Zmieniono';
$lang['changed_field'] = 'zmieniono';
$lang['by'] = 'przez';
$lang['from'] = 'z';
$lang['to'] = 'na';
$lang['diff'] = 'różnice';
$lang['comment'] = 'Dodaj';
$lang['replay'] = 'Odpowiedz';
$lang['edit'] = 'Edytuj';
$lang['change_task_state'] = 'Zmień status zadania';
$lang['replay_to'] = 'Odpowiedź na';
$lang['quoted_in'] = 'Odpowiedzi';

$lang['error_issue_id_not_specifed'] = 'Nie podałeś numeru wiersza, który chcesz odczytać.';
$lang['error_issue_id_unknown'] = 'Wiersz, który próbujesz odczytać nie istnieje.';
$lang['error_db_connection'] = 'Brak połączenia z bazą danych.';
$lang['error_issue_insert'] = 'Nie można dodać nowego problemu.';
$lang['error_task_add'] = 'Nie masz uprawnień aby dodawać zadania.';
$lang['error_table_unknown'] = 'Wybrana tabela nie istnieje.';
$lang['error_report_unknown'] = 'Wybrana raport nie istnieje.';
$lang['error_issue_report'] = 'Nie masz uprawnień aby zgłaszać nowe problemy.';
$lang['error_entity'] = 'Nie masz uprawnień aby dodawać nowe źródła zgłoszeń.';
$lang['error_issues'] = 'Nie masz uprawnień aby przeglądać problemy.';
$lang['error_no_permission'] = 'Nie masz uprawnień aby przeglądać tę stronę.';

$lang['vald_type_required'] = 'Określ typ problemu.';
$lang['vald_entity_required'] = 'Wybierz źródło zgłoszenia z listy.';
$lang['vald_title_required'] = 'Podaj tytuł.';
$lang['vald_title_too_long'] = 'Za długi tytuł, max: %d znaków.';
$lang['vald_title_wrong_chars'] = 'Niedozwolone znaki w tytule. Dozwolone są: litery, cyfry, spacje, myślniki, podkreślniki, kropki i przecinki.';
$lang['vald_executor_required'] = 'Wybierz istniejącego użytkownika albo pozostaw problem nieprzypisany do nikogo.';
$lang['vald_coordinator_required'] = 'Koordynator musi być użytkownikiem wiki.';

$lang['vald_desc_required'] = 'Opisz problemu.';
$lang['vald_desc_too_long'] = 'Za długi opis problemu, max: %d znaków.';
$lang['vald_opinion_too_long'] = 'Za długa ocena skuteczności, max: %d znaków.';
$lang['vald_opinion_required'] = 'Ocena skuteczności jest wymagana.';
$lang['vald_cannot_give_opinion'] = 'Nie możesz ocenić skuteczności jeżeli problem pozostanje otwarty.';
$lang['vald_cannot_give_reason'] = 'Nie zmieniłeś statusu zadania';


$lang['vald_content_required'] = 'Wpisz tekst';
$lang['vald_content_too_long'] = 'Za długi tekst, max: %d.';
$lang['vald_replay_to_not_exists'] = 'Comment nie istnieje.';
$lang['vald_state_required'] = 'Określ status problemu.';
$lang['vald_state_tasks_not_closed'] = 'Nie możesz zmieniać statusu problemu, jeżeli istnieją jakieś niezamknięte zadania.';


$lang['vald_task_state_required'] = 'Określ status zadania.';
$lang['vald_task_state_tasks_not_closed'] = 'Nie możesz zamknąć problemu przed zamknięciem wszystkich zadań. Otwarte zadania: %t.';

$lang['vald_executor_not_exists'] = 'Podany wykonawca nie jest użytkownikiem BEZ.';
$lang['vald_cost_too_big'] = 'Zbyt wysoki koszt, max: %d.';
$lang['vald_cost_wrong_format'] = 'Koszt powinien być liczbą całkowitą.';
$lang['vald_class_required'] = 'Podaj klasę zadania.';

$lang['vald_days_should_be_numeric'] = 'Ilość dni musi być liczbą.';

$lang['vald_entity_too_long'] = 'Pojedyńcze źródło zgłoszenia może mieć najwyżej %d znaków.';
$lang['vald_entity_no_ascii'] = 'Zródło zgłoszenia zawiera znaki spoza ASCII.';


$lang['vald_type_wrong_chars'] = 'Typ musi składać się z samych liter i cyfr.';
$lang['vald_type_required'] = 'Musisz podać nazwę typu w obu językach.';

$lang['priority_marginal'] = 'marginalny';
$lang['priority_important'] = 'istotny';
$lang['priority_crucial'] = 'krytyczny';

$lang['type_complaint'] = 'reklamacja';
$lang['type_noneconformity'] = 'niezgodność';
$lang['type_noneconformity_internal'] = 'niezgodność';
$lang['type_noneconformity_customer'] = 'reklamacja od klienta';
$lang['type_noneconformity_supplier'] = 'reklamacja do dostawcy';
$lang['type_opportunity'] = 'szansa';
$lang['type_threat'] = 'zagrożenie';

$lang['state_proposal'] = 'propozycja';
$lang['state_opened'] = 'otwarta';
$lang['state_rejected'] = 'odrzucona';
$lang['state_closed'] = 'zamknięta';

$lang['reject_issue'] = 'Odrzuć problem';
$lang['close_issue'] = 'Zamknij problem';


$lang['just_now'] = 'przed chwilą';
$lang['seconds'] = 'sek.';
$lang['minutes'] = 'min.';
$lang['hours'] = 'godz.';
$lang['days'] = 'dn.';
$lang['ago'] = 'temu';

$lang['issue_closed_com'] = 'Problem został zamknięty %d, dalsze zmiany nie są możliwe.';
$lang['reopen_issue'] = 'Zmień status problemu';
$lang['add'] = 'Dodaj';

$lang['class'] = 'Działanie';

$lang['open'] = 'Otwarte';
$lang['closed'] = 'Zamknięte';

$lang['cost'] = 'Koszt(zł)';

$lang['executor'] = 'Wykonawca';

$lang['task_state'] = 'Status';
$lang['reason'] = 'Przyczyna odrzucenia';

$lang['task_added'] = 'Zadanie dodane';
$lang['task_changed'] = 'Zadanie zmienione';
$lang['task_rejected_header'] = 'Zadanie odrzucone';
$lang['task_closed'] = 'Zadanie zakończone';
$lang['task_reopened'] = 'Zadanie ponownie otwarte';
$lang['comment_added'] = 'Komentarz dodany';
$lang['comment_changed'] = 'Komentarz zmieniony';

$lang['replay_by_task'] = 'Odpowiedz, dodając zadanie';
$lang['change_made'] = 'Zmiana wprowadzona';

$lang['change_comment'] = 'Popraw komentarz';
$lang['change_comment_button'] = 'Popraw komentarz';
$lang['change_task'] = 'Zmodyfikuj zadanie';
$lang['change_task_button'] = 'Zmień zadanie';

$lang['preview'] = 'starsze';
$lang['next'] = 'nowsze';

$lang['version'] = 'Wersja';

$lang['comment_noun'] = 'Komentarz';
$lang['change'] = 'Zmiana';
$lang['task'] = 'Zadanie';

$lang['change_state_button'] = 'Zmień status';


$lang['correction'] = 'Korekcyjne';
$lang['corrective_action'] = 'Korygujące';
$lang['preventive_action'] = 'Zapobiegawcze';

$lang['correction_h'] = 'Działania korekcyjne';
$lang['corrective_action_h'] = 'Działania korygujące';
$lang['preventive_action_h'] = 'Działania zapobiegawcze';

$lang['correction_add'] = 'Dodaj korekcję';
$lang['corrective_action_add'] = 'Dodaj działanie korygujące';
$lang['preventive_action_add'] = 'Dodaj działanie zapobiegawcze';

$lang['show_all_tasks'] = 'Pokaż wszystkie działania';


$lang['none_comment'] = 'brak(komentarz)';
$lang['manpower'] = 'Pracownicy';
$lang['method'] = 'Metoda';
$lang['machine'] = 'Maszyna';
$lang['material'] = 'Materiał';
$lang['managment'] = 'Zarządzanie';
$lang['measurement'] = 'Pomiar';
$lang['money'] = 'Pieniądze';
$lang['environment'] = 'Środowisko';
$lang['communication'] = "Komunikacja";

$lang['task_opened'] = 'Otwarte';
$lang ['task_done'] = 'Wykonane';
$lang ['task_rejected'] = 'Odrzucone';

$lang ['task_do'] = 'Wykonaj';
$lang ['task_reject'] = 'Odrzuć';

$lang['reason_reopen'] = 'Przyczyna ponownego otwarcia'; 
$lang['reason_done']  = 'Przyczyna zakończenia';
$lang['reason_reject'] = 'Przyczyna odrzucenia';

$lang['issue_created'] = 'Zgłoszono';
$lang['issue_closed'] = 'Zamknięto';
$lang['issue_reopened'] = 'Ponownie otwarto problem';
$lang['issue_rejected'] = 'Odrzucono';

$lang['issue_reopen'] = 'Otwórz ponownie';

$lang['today'] = 'Dzisiaj';
$lang['yesterday'] = 'Wczoraj';

$lang['task_for'] = 'dla';
$lang['content'] = 'Opis';

$lang['8d_report'] = 'Raport 8D';
$lang['8d_report_for'] = 'dla';
$lang['open_date'] = 'Data otwarcia';
$lang['close_date'] = 'Data zamknięcia';
$lang['1d'] = '1D - Zespół';
$lang['2d'] = '2D - Problem';
$lang['3d'] = '3D - Przyczyna';
$lang['4d'] = '4D - Korekcja';
$lang['5d'] = '5D - Działania korygujące';
$lang['6d'] = '6D - Działania zapobiegawcze';
$lang['7d'] = '7D - Ocena skuteczności';
$lang['8d'] = '8D - Zakończenie';

$lang['number'] = 'Ilość';
$lang['true_date'] = 'Data';

$lang['report_issues'] = 'Problemy';
$lang['report_tasks'] = 'Zadania';
$lang['report_causes'] = 'Przyczyny';
$lang['report_priority'] = 'Czas reakcji';
$lang['report_subhead'] = 'dotyczy zamkniętych problemów i zadań';

$lang['priority'] = 'Priorytet';
$lang['average'] = 'Średnia';

$lang['totalcost'] = 'Wartość';
$lang['report_total'] = 'Razem';


$lang['show'] = 'Pokaż';

$lang['by_last_activity'] = 'Otwarte, według ostatnio zmienionych';
$lang['issues_by_last_activity'] = 'Problemy otwarte według ostatnio zmienionych';

$lang['ns'] = 'n.d.';

$lang['ended'] = 'Zakończone';

//Mail
$lang['new_task'] = "Nowe zadanie";
$lang['new_issue'] = "Nowy problem";
$lang['send_mail'] = "Wyślij";

$lang['average_days'] = "Średni czas zamykania zadań w dniach";

$lang['bds_switch_lang'] = "English version";

$lang['comments'] = 'Komentarze';
$lang['causes'] = 'Przyczyny';
$lang['action'] = 'Akcja';

$lang['cause_added'] = "Przyczyna dodana";
$lang['cause_noun'] = "Przyczyna";
$lang['change_cause_button'] = "Popraw przyczynę";
$lang['added'] = 'Dodana';

$lang['delete'] = 'Usuń';
$lang['info_no_causes_added'] = 'Musisz dodać co najmniej jedną przyczynę, aby dodawać działania korygujące i zapobiegawcze.';
$lang['info_no_all_tasks_closed'] = 'Musisz zamknąć wszystkie zadania, zanim zamkniesz problem.';


$lang['types_manage'] = 'Typy problemów';

$lang['sort'] = 'Sortuj';
$lang['cancel'] = 'Anuluj';

$lang['menu_close_task'] = 'Moje zadania (%d)';
$lang['menu_close_issue'] = 'Moje problemy (%d)';
$lang['menu_comment_issue'] = 'Otwarte problemy (%d)';

$lang['do_you_want_remove'] = 'Czy na pewno chcesz usunąć ten rekord?';
$lang['yes'] = 'Tak';
$lang['no'] = 'Nie';

$lang['all'] = 'wszystkie';
$lang['year'] = 'Rok';
$lang['in_year'] = 'w roku';
$lang['filter'] = 'Filtruj';

$lang['close_issues'] = 'Moje problemy';
$lang['close_tasks'] = 'Moje zadania';

$lang['proposals'] = 'Propozycje';

$lang['show_tasks_hidden'] = 'Pokaż zadania wykonane i odrzucone';
$lang['add_task'] = 'Dodaj zadanie';

$lang['save_without_changing_date'] = 'Zapisz bez zmiany daty';

$lang['sort_by_open_date'] = 'Sortuj wg daty otwarcia';

$lang['issue_is_proposal'] = 'Nie można dodawać przyczyn ani zadań dopóki problem jest propozycją.';

$lang['casue_cant_remove'] = 'Nie możesz usunąć przyczyny z dodanymi zadaniami.';

$lang['add_cause'] = 'Dodaj przyczynę';
$lang['add_correction'] = 'Dodaj korekcję';

$lang['cause'] = 'przyczyna';
$lang['potential_cause'] = 'potencjalna przyczyna';

$lang['cause_type'] = 'Typ przyczyny';

$lang['cause_type_default'] = 'rzeczywista';
$lang['cause_type_potential'] = 'potencjalna';

$lang['evaluation'] = 'Ocena';

$lang['rr_report'] = 'Raport RR';

$lang['rr_team'] = 'Zespół oceny ryzyka';
$lang['rr_desc'] = 'Opis ryzyka';
$lang['rr_eval'] = 'Ocena ryzyka';
$lang['rr_suceval'] = 'Ocena skuteczności';

$lang['correction_nav'] = 'Korekcje';
$lang['reaction'] = 'Reakcja';

$lang['closed_tasks'] = 'Zadania zamknięte';
$lang['root_causes'] = 'Karegorie przyczyn';

$lang['version'] = 'wersja';

$lang['issues_juxtaposition'] = 'Zestawienie problemów';
$lang['tasks_juxtaposition'] = 'Zestawienie zadań';

$lang['issue_unclosed_tasks'] = 'Nie można zamknąć problemu, dopóki zawiera on nierozwiązane zadania.';
