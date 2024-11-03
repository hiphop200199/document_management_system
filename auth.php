<?php
session_start();
require_once('./parts/db.php');
date_default_timezone_set('Asia/Taipei');//設定預設時區
class Auth
{
    private $conn; //承接服務的私有屬性
    public function __construct(PDO $conn) //注入服務
    {
        $this->conn = $conn; //承接服務
    }
    public function getDateFromFrontend()
    {
        $request_body = file_get_contents('php://input');
    
        $data = json_decode($request_body, true);
        
        switch ($data['task']) {
            case 'register':
                $this->register($data);
                break;
            case 'login':
                $this->login($data);
                break;
            case 'logout':
                $this->logout();
                break;
            case 'forgot-password':
                $this->forgotpassword($data);
                break;
            case 'reset-password':
                $this->resetpassword($data);
                break;    
        }
    }
    private function login($data)
    {
        header('Content-Type:application/json'); //回傳json格式
        $email = $data['email'];
        $password = $data['password'];

        if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
            session_regenerate_id();//參數true會刪掉現有的session資料，會導致無法辨識session固定攻擊以及程序競爭問題(程序競爭為同時多道程序搶著使用某一資源，而沒有等當下的session處理完，需要用lock方式避免競爭問題)
            echo json_encode(['message' => 'email格式不正確.']);
            exit;
        }
        if(!preg_match('/[A-Z]{1,}[a-z]{1,}[0-9]{1,}\W{1,}/',strip_tags(trim($password)))){
            session_regenerate_id();
            echo json_encode(['message' => '密碼格式不正確.']);
            exit;
        }
        $check_email_sql = 'select name,email,password,identity,delete_flag from users where email = ? ';
        $statement = $this->conn->prepare($check_email_sql);
        $statement->execute([$email]);
        if ($statement->rowCount() > 0) {
            session_regenerate_id();
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            $check_password = password_verify($password,$row['password']);//驗證雜湊跟密碼是否符合
            if(!$check_password){
                session_regenerate_id(); //更新sessionID
                echo json_encode(['message' => '密碼不正確.']);
                exit;
            }elseif($row['delete_flag']==1){
                session_regenerate_id();
                echo json_encode(['message'=>'此帳號已無效']);
            }else{
                session_regenerate_id(); //更新sessionID
                $_SESSION['user'] = $row['name'];
                $_SESSION['identity'] = $row['identity'];
                echo json_encode(['message' => 'login success.', 'redirect' => 'dashboard.php']);
                exit;
            }
           
        } else {
            session_regenerate_id();
            echo json_encode(['message' => '無此帳號，需註冊.']);
            exit;
        }
    }
    private function logout()
    {
        $_SESSION = [];
        session_destroy(); //破壞session
        echo json_encode(['message'=>'logout success.','reload'=>'true']);
        exit;
    }
    private function register($data)
    {
        header('Content-Type:application/json'); //回傳json格式
        $name = strip_tags(trim($data['name']));
        $email = $data['email'];
        $password = $data['password'];

        if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
            session_regenerate_id();//參數true會刪掉現有的session資料，會導致無法辨識session固定攻擊以及程序競爭問題(程序競爭為同時多道程序搶著使用某一資源，而沒有等當下的session處理完，需要用lock方式避免競爭問題)
            echo json_encode(['message' => 'email格式不正確.']);
            exit;
        }
        if(!preg_match('/[A-Z]{1,}[a-z]{1,}[0-9]{1,}\W{1,}/',strip_tags(trim($password)))){
            session_regenerate_id();
            echo json_encode(['message' => '密碼格式不正確.']);
            exit;
        }
        $check_email_sql = 'select email from users where email = ?';
        $statement = $this->conn->prepare($check_email_sql); 
        $statement->execute([$email]);
        if ($statement->rowCount() > 0) {
            session_regenerate_id();
            echo json_encode(['message' => '此email已經註冊過']);
            exit;
        } else {
            $hashed_password = password_hash(strip_tags(trim($password)), PASSWORD_DEFAULT);
            $time = date("Y-m-d H:i:s");  
            $create_user_sql = 'insert into users values (?,?,?,?,?,?,?,?)';
            $statement = $this->conn->prepare($create_user_sql);
            $statement->execute([null,$name,$email,$hashed_password,'normal',0,$time,$time]);
            if ($statement->rowCount() > 0) {
                session_regenerate_id(); //更新sessionID
                $_SESSION['user'] = $name;
                $_SESSION['identity'] = 'normal';
                echo json_encode(['message' => 'register success.', 'redirect' => 'dashboard.php']);
                exit;
            }
        }


       
      
        
        
    }
    private function forgotpassword($data)
    {
        header('Content-Type:application/json'); //回傳json格式
        $email = $data['email'];
        if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
            session_regenerate_id();//參數true會刪掉現有的session資料，會導致無法辨識session固定攻擊以及程序競爭問題(程序競爭為同時多道程序搶著使用某一資源，而沒有等當下的session處理完，需要用lock方式避免競爭問題)
            echo json_encode(['message' => 'email格式不正確.']);
            exit;
        }
        $check_email_sql = 'select name,email,identity,delete_flag from users where email = ? ';
        $statement = $this->conn->prepare($check_email_sql);
        $statement->execute([$email]);
        if ($statement->rowCount() > 0) {
            session_regenerate_id();
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            if($row['delete_flag']==1){
                session_regenerate_id();
                echo json_encode(['message'=>'此帳號已無效']);
                exit;
            }else{
                session_regenerate_id(); //更新sessionID
                $_SESSION['user'] = $row['name'];
                $_SESSION['identity'] = $row['identity'];
                $_SESSION['email'] = $row['email'];
                echo json_encode(['message' => 'please wait.', 'redirect' => 'reset-password.php']);
                exit;
            }
           
        } else {
            session_regenerate_id();
            echo json_encode(['message' => '無此帳號，需註冊.']);
            exit;
        }
    }
    private function resetpassword($data)
    {
        header('Content-Type:application/json'); //回傳json格式
        $password = $data['password'];
        if(!preg_match('/[A-Z]{1,}[a-z]{1,}[0-9]{1,}\W{1,}/',strip_tags(trim($password)))){
            session_regenerate_id();
            echo json_encode(['message' => '密碼格式不正確.']);
            exit;
        }
        $hashed_password = password_hash(strip_tags(trim($password)), PASSWORD_DEFAULT);
        $time = date("Y-m-d H:i:s");     
        $new_password_sql = 'update users set password = ?,updated_at = ? where email = ?';
        $statement = $this->conn->prepare($new_password_sql);
        $statement->execute([$hashed_password,$time, $_SESSION['email']]);
        if ($statement->rowCount() > 0) {
            session_regenerate_id();
            unset($_SESSION['email']);
            echo json_encode(['message' => 'reset password success.', 'redirect' => 'dashboard.php']);
            exit;
        }
    }
}
$auth = new Auth($conn);

$auth->getDateFromFrontend();

$auth = null;
