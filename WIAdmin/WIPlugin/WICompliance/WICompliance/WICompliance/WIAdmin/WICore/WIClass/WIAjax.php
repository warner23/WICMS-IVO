<?php
include_once 'WI.php';

//csrf protection
if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') 
    die("Sorry bro!");

$url = parse_url( isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
if( !isset( $url['host']) || ($url['host'] != $_SERVER['SERVER_NAME']))
    die("Sorry bro!");

$action = isset($_POST['action']) ? $_POST['action'] : null;

switch ($action) {
	case 'checkLogin':
		$logged = $login->userLogin($_POST['username'], $_POST['password']);
        if($logged === true)
            echo json_encode(array(
                'status' => 'success',
                'page'   => get_redirect_page()
            ));
		break;

    case 'staffLogin':
        $logged = $login->StaffLogin($_POST['username'], $_POST['password']);

        break;
        
    case "registerUser":
        $register->register($_POST['User']);
        break;

    case "registerStaff":
        $register->registerStaff($_POST['User']);
        break;
        
    case "resetPassword":
        $register->resetPassword($_POST['newPass'], $_POST['key']);
        break;
        
    case "forgotPassword":
        $result = $register->forgotPassword($_POST['email']);
        if ( $result !== TRUE )
            echo $result;
        break;
        
    case "postComment":
        $WIComment = new WIComment();
        echo $WIComment->insertComment(WISession::get("user_id"), $_POST['comment']);
        break;
        
    case "updatePassword":
        $user = new WIUser(WISession::get("user_id"));
        $user->updatePassword($_POST['oldpass'], $_POST['newpass']);
        break;
        
    case "updateDetails":
        $user = new WIUser(WISession::get("user_id"));
        $user->updateDetails($_POST['details']);
        break;
        
    case "changeRole":
        onlyAdmin();

        $user = new WIUser($_POST['userId']);
        echo ucfirst($user->changeRole());
        break;
        
    case "deleteUser":
        onlyAdmin();

        $user = new WIUser($_POST['userId']);
        $user->deleteUser();
        break;
    
    case "getUserDetails":
        onlyAdmin();

        $user = new WIUser($_POST['userId']);
        echo json_encode( $user->getAll() );
        break;

    case "addRole": 
        onlyAdmin();

        $role = new WIRole();
        echo json_encode( $role->add($_POST['role']) );
        break;

    case "deleteRole":
        onlyAdmin();

        $role = new WIRole();
        $role->delete($_POST['roleId']);
        break;


    case "addUser":
        onlyAdmin();

        $user = new WIUser(null);
        echo json_encode( $user->add($_POST) );
        break;

    case "updateUser":
        onlyAdmin();

        $user = new WIUser($_POST['userId']);
        $user->updateUser($_POST);
        break;

    case "banUser":
        onlyAdmin();

        $user = new WIUser($_POST['userId']);
        $user->updateInfo(array( 'banned' => 'Y' ));
        break;

    case "unbanUser":
        onlyAdmin();

        $user = new WIUser($_POST['userId']);
        $user->updateInfo(array( 'banned' => 'N' ));
        break;

    case "getUser":
        onlyAdmin();

        $user = new WIUser($_POST['userId']);
        echo json_encode($user->getAll());
        break;

                case "getCalendar":
        $calendar = new WICalendar();
        $calendar->getCalendar($_POST['year'], $_POST['month']) ;
        break;

        case "addEvent":
        $calendar = new WICalendar();
        $calendar->addEvent($_POST['date']) ;
        break;

         case "getEvents":
        $calendar = new WICalendar();
        $calendar->getEvents($_POST['date']) ;
        break;


        case "quizAnswers":
        $quiz = new WIQuiz();
        $quiz->InputAnswer($_POST['id'], $_POST['selected'],$_POST['no'] );
        break;

        case "easyQuizAnswers":
        $Kquiz = new WIKitchenQuiz();
        $Kquiz->InputEasyAnswer($_POST['id'], $_POST['selected'],$_POST['no'],$_POST['type'] );
        break;

        case "modQuizAnswers":
        $Kquiz = new WIKitchenQuiz();
        $Kquiz->InputModAnswer($_POST['id'], $_POST['selected'],$_POST['no'] );
        break;

        case "advQuizAnswers":
        $Kquiz = new WIKitchenQuiz();
        $Kquiz->InputAdvAnswer($_POST['id'], $_POST['selected'],$_POST['no'] );
        break;

        case "quizSubmit":
        $quiz = new WIQuiz();
        $quiz->QuizSubmit($_POST['id'], $_POST['selected'],$_POST['no'] );
        break;

        case "quizEasySubmit":
        $Kquiz = new WIKitchenQuiz();
        $Kquiz->QuizEasySubmit($_POST['id'], $_POST['selected'],$_POST['no'],$_POST['type'] );
        break;

        case "quizModSubmit":
        $Kquiz = new WIKitchenQuiz();
        $Kquiz->QuizModSubmit($_POST['id'], $_POST['selected'],$_POST['no'] );
        break;

        case "quizAdvSubmit":
        $Kquiz = new WIKitchenQuiz();
        $Kquiz->QuizAdvSubmit($_POST['id'], $_POST['selected'],$_POST['no'] );
        break;

        case "EasySaveScore":
        $Kquiz = new WIKitchenQuiz();
        $Kquiz->EasySaveScore($_POST['score'], $_POST['name']);
        break;

        case "ModSaveScore":
        $Kquiz = new WIKitchenQuiz();
        $Kquiz->ModSaveScore($_POST['score'], $_POST['name']);
        break;

        case "AdvSaveScore":
        $Kquiz = new WIKitchenQuiz();
        $Kquiz->AdvSaveScore($_POST['score'], $_POST['name']);
        break;


        case "easyBarQuizAnswers":
        $Bquiz = new WIBarQuiz();
        $Bquiz->InputEasyAnswer($_POST['id'], $_POST['selected'],$_POST['no'] );
        break;

        case "modBarQuizAnswers":
        $Bquiz = new WIBarQuiz();
        $Bquiz->InputModAnswer($_POST['id'], $_POST['selected'],$_POST['no'] );
        break;

        case "advBarQuizAnswers":
        $Bquiz = new WIBarQuiz();
        $Bquiz->InputAdvAnswer($_POST['id'], $_POST['selected'],$_POST['no'] );
        break;


        case "quizEasyBarSubmit":
        $Kquiz = new WIBarQuiz();
        $Kquiz->QuizEasySubmit($_POST['id'], $_POST['selected'],$_POST['no'] );
        break;

        case "quizModBarSubmit":
        $Kquiz = new WIBarQuiz();
        $Kquiz->QuizModSubmit($_POST['id'], $_POST['selected'],$_POST['no'] );
        break;

        case "quizAdvBarSubmit":
        $Bquiz = new WIBarQuiz();
        $Bquiz->QuizAdvSubmit($_POST['id'], $_POST['selected'],$_POST['no'] );
        break;

        case "EasyBarSaveScore":
        $Bquiz = new WIBarQuiz();
        $Bquiz->EasySaveScore($_POST['score'], $_POST['name']);
        break;

        case "ModBarSaveScore":
        $Bquiz = new WIBarQuiz();
        $Bquiz->ModSaveScore($_POST['score'], $_POST['name']);
        break;

        case "AdvBarSaveScore":
        $Bquiz = new WIBarQuiz();
        $Bquiz->AdvSaveScore($_POST['score'], $_POST['name']);
        break;

        case "deliveryChecks":
        $compliance = new WICompliance();
        $compliance->deliveryChecksSave( $_POST['User']);
        break;

        case "deepCleaning":
        $compliance = new WICompliance();
        $compliance->deepCleaningSave( $_POST['User']);
        break;

        case "dailyCleaning":
        $compliance = new WICompliance();
        $compliance->dailyCleaningSave( $_POST['User']);
        break;

        case "morningChecks":
        $compliance = new WICompliance();
        $compliance->morningChecksSave( $_POST['User']);
        break;

        case "eveningChecks":
        $compliance = new WICompliance();
        $compliance->eveningChecksSave( $_POST['User']);
        break;

        case "GetMorningChecks":
        $compliance = new WICompliance();
        $compliance->EditMorningChecks($_POST['User']);
        break;

        case "GetEveningChecks":
        $compliance = new WICompliance();
        $compliance->EditEveningChecks($_POST['User']);
        break;

        case "GetDailyCleaning":
        $compliance = new WICompliance();
        $compliance->EditDailyCleaning($_POST['User']);
        break;

        case "GetDeepCleaning":
        $compliance = new WICompliance();
        $compliance->EditDeepCleaning($_POST['User']);
        break;

        case "GetDeliveryChecks":
        $compliance = new WICompliance();
        $compliance->EditDeliveryChecks($_POST['User']);
        break;

        
        default:
        
        break;
}
$action = isset($_GET['action']) ? $_GET['action'] : null;
switch($action){
        
        case "quizStart":
        $quiz = new WIQuiz();
        $quiz->Quiz();
        break;

        case "quizEasyStart":
        $Kquiz = new WIKitchenQuiz();
        $Kquiz->EasyQuiz();
        break;

        case "quizModStart":
        $Kquiz = new WIKitchenQuiz();
        $Kquiz->ModQuiz();
        break;

        case "quizAdvStart":
        $Kquiz = new WIKitchenQuiz();
        $Kquiz->AdvQuiz();
        break;

        case "quizBarEasyStart":
        $Bquiz = new WIBarQuiz();
        $Bquiz->EasyQuiz();
        break;

        case "quizBarModStart":
        $Bquiz = new WIBarQuiz();
        $Bquiz->ModQuiz();
        break;

        case "quizBarAdvStart":
        $Bquiz = new WIBarQuiz();
        $Bquiz->AdvQuiz();
        break;

        case "getResults":
        $quiz = new WIQuiz();
        $quiz->getResults();
        break;

        case "getEasyResults":
        $Kquiz = new WIKitchenQuiz();
        $Kquiz->getEasyResults();
        break;

        case "getModResults":
        $Kquiz = new WIKitchenQuiz();
        $Kquiz->getModResults();
        break;

        case "getAdvResults":
        $Kquiz = new WIKitchenQuiz();
        $Kquiz->getAdvResults();
        break;

        case "getBarEasyResults":
        $Bquiz = new WIBarQuiz();
        $Bquiz->getEasyResults();
        break;

        case "getBarModResults":
        $Bquiz = new WIBarQuiz();
        $Bquiz->getModResults();
        break;

        case "getBarAdvResults":
        $Bquiz = new WIBarQuiz();
        $Bquiz->getAdvResults();
        break;

        case "RevealEasyBarAnswers":
        $Bquiz = new WIBarQuiz();
        $Bquiz->RevealEasyBarAnswers();
        break;

        case "RevealModBarAnswers":
        $Bquiz = new WIBarQuiz();
        $Bquiz->RevealModBarAnswers();
        break;

        case "RevealAdvBarAnswers":
        $Bquiz = new WIBarQuiz();
        $Bquiz->RevealAdvBarAnswers();
        break;

         case "RevealEasyAnswers":
        $Kquiz = new WIKitchenQuiz();
        $Kquiz->RevealEasyAnswers();
        break;

        case "RevealModAnswers":
        $Kquiz = new WIKitchenQuiz();
        $Kquiz->RevealModAnswers();
        break;

        case "RevealAdvAnswers":
        $Kquiz = new WIKitchenQuiz();
        $Kquiz->RevealAdvAnswers();
        break;

        case "sosrandomise":
        $barSide = new WIBarSide();
        $barSide->sosrandomise();
        break;
        
       default:
       break;
   }

function onlyAdmin() {
    $login = new WILogin();
    if ( ! $login->isLoggedIn() ) exit();

    $loggedUser = new WIUser(WISession::get("user_id"));
    if( ! $loggedUser->isAdmin() ) exit();
}