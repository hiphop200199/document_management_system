<?php
session_start();
require_once('./parts/db.php');
class Auth
{
    private $conn; //承接服務的私有屬性
    public function __construct(PDO $conn) //注入服務
    {
        $this->conn = $conn; //承接服務
    }
    public function login()
    {
        header('Content-Type:application/json'); //回傳json格式
        $account = filter_var($_POST['account'], FILTER_VALIDATE_EMAIL); //驗證email格式
        if (!$account) {
            session_regenerate_id(true);
            echo json_encode(['message' => 'email格式不正確.']);
            exit;
        }
        $check_account_sql = 'select email,password from users where email = ? ';
        $statement = $this->conn->prepare($check_account_sql);
        $statement->execute([$account]);
        if ($statement->rowCount() > 0) {
            session_regenerate_id(true);
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            $check_password = password_verify($_POST['password'],$row['password']);//驗證雜湊跟密碼是否符合
            if(!$check_password){
                session_regenerate_id(true); //更新sessionID
                echo json_encode(['message' => '密碼不正確.']);
                exit;
            }else{
                session_regenerate_id(true); //更新sessionID
                $_SESSION['user'] = $account;
                echo json_encode(['message' => 'login success.', 'redirect' => 'navigate.php']);
                exit;
            }
           
        } else {
            session_regenerate_id(true);
            echo json_encode(['message' => '無此帳號，需註冊.']);
            exit;
        }
    }
    public function logout()
    {
        unset($_SESSION['user']); //刪除變數
        session_destroy(); //破壞session
        header('Location:index.php'); //轉址回首頁
    }
    public function register()
    {
        header('Content-Type:application/json'); //回傳json格式
        $account = filter_var($_POST['account'], FILTER_VALIDATE_EMAIL); //驗證email格式
        $password = password_hash(strip_tags(trim($_POST['password'])), PASSWORD_DEFAULT); //去空白並去掉html,xml,php標籤並雜湊


        if (!$account) {
            session_regenerate_id(true);
            echo json_encode(['message' => 'email格式不正確.']);
            exit;
        }
        $check_email_sql = 'select email from users where email = ?';
        $statement = $this->conn->prepare($check_email_sql); //準備pdo敘述
        // $statement->bindParam('email', $account, PDO::PARAM_STR);//綁定一個參數到pdo敘述，特定資料型態再用
        $statement->execute([$account]);
        if ($statement->rowCount() > 0) {
            session_regenerate_id(true);
            echo json_encode(['message' => '此email已經註冊過']);
            exit;
        } else {
            $create_user_sql = 'insert into users values (?,?,?)';
            $statement = $this->conn->prepare($create_user_sql);
            $statement->execute([null, $account, $password]);
            if ($statement->rowCount() > 0) {
                session_regenerate_id(true); //更新sessionID
                $_SESSION['user'] = $account;
                echo json_encode(['message' => 'register success.', 'redirect' => 'navigate.php']);
                exit;
            }
        }
    }
    public function forgotpassword()
    {
        header('Content-Type:application/json'); //回傳json格式
        $account = filter_var($_POST['account'], FILTER_VALIDATE_EMAIL); //驗證email格式
        if (!$account) {
            session_regenerate_id(true);
            echo json_encode(['message' => 'email格式不正確.']);
            exit;
        }
        $check_email_sql = 'select email from users where email = ?';
        $statement = $this->conn->prepare($check_email_sql); //準備pdo敘述
        $statement->execute([$account]);
        if ($statement->rowCount() > 0) {
            session_regenerate_id(true); //更新sessionID
            $_SESSION['user'] = $account;
            echo json_encode(['message' => 'email validate success.', 'redirect' => 'reset-password.php']);
            exit;
        } else {
            session_regenerate_id(true);
            echo json_encode(['message' => '無此帳號，需註冊.']);
            exit;
        }
    }
    public function resetpassword()
    {
        $account = $_SESSION['user'];
        $password = password_hash(strip_tags(trim($_POST['password'])), PASSWORD_DEFAULT); //去空白並去掉html,xml,php標籤並雜湊
        $new_password_sql = 'update users set password = ? where email = ?';
        $statement = $this->conn->prepare($new_password_sql);
        $statement->execute([$password, $account]);
        if ($statement->rowCount() > 0) {
            session_regenerate_id(true);
            $_SESSION['user'] = $account;
            echo json_encode(['message' => 'reset password success.', 'redirect' => 'navigate.php']);
            exit;
        }
    }
}
$auth = new Auth($conn);
switch ($_POST['type']) {
    case 'register':
        $auth->register();
        break;
    case 'login':
        $auth->login();
        break;
    case 'logout':
        $auth->logout();
        break;
    case 'forgot-password':
        $auth->forgotpassword();
        break;
    case 'reset-password':
        $auth->resetpassword();
        break;
}

$auth = null;
