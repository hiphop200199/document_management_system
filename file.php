<?php
require_once(__DIR__.'/parts/db.php');
date_default_timezone_set('Asia/Taipei');//設定預設時區
class File
{
    private $conn;
    
    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }
    public function getDataFromFrontend()
    {
        if(isset($_FILES['file'])){
            $this->createItem();
           exit;
        } 
              
        $request_body = file_get_contents('php://input');
       
        $data = json_decode($request_body, true);
       
        switch ($data['task']) {
            case 'getItems':
               $this->getItems($data);
                break;
            case 'deleteItem':
                $this->deleteItem($data);
                break;
        }
    }
    private function createItem() 
    {
        header('Content-Type:application/json'); //回傳json格式
        $target_dirctory = 'uploads/';
        $target_file = $target_dirctory. basename($_FILES['file']['name']);//取得檔名部分
        $file = $_FILES['file'];   
        $file_type = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) ;//用來分析路徑的資訊
        $file_name = basename($_FILES['file']['name']);
        if(file_exists($target_file)){//檢查檔案是否存在
            echo json_encode(['message'=>'file already exist.']);
            exit;
        }
        if($file_type!=='pdf'){//檢查檔案格式
            echo json_encode(['message' => 'pdf only.']);
            exit;
        }
        if($file['size']>5000000){//檢查檔案大小
            echo json_encode(['message'=> 'file is too large.']);
            exit;
        }
        if(move_uploaded_file($file['tmp_name'], $target_file)){//檢查是不是php正常上傳機制上傳的檔案，如果是就移動到指定的路徑
            $time = date("Y-m-d H:i:s");
            $sql = 'insert into documents values(?,?,?,?,?,?);';          
            $statement = $this->conn->prepare($sql);
            $statement->execute([null,$file_name,$target_file,0,$time,$time]);
            if ($statement->rowCount() > 0) {
                echo json_encode(['message' => 'create success', 'reload' => 'true']);
                exit;
            }else{
                echo json_encode(['message'=> 'something wrong.']);
                exit;
            }
        }else{
            echo json_encode(['message'=> 'file upload failed.']);
            exit;
        }
       
       
    }
    
   
    private function deleteItem($data)
    {
        $id = strip_tags(trim($data['id']));
        $sql = 'update documents set delete_flag = 1 where id = ? ';
        $statement = $this->conn->prepare($sql);
        $statement->execute([$id]);
        if ($statement->rowCount() > 0) {
            echo json_encode(['message' => 'delete success', 'reload' => 'true']);
            exit;
        }
    }
    private function getItems($data)
    {
        header('Content-Type:application/json'); //回傳json格式
        $keyword = strip_tags(trim($data['keyword']));
        $startDate = strip_tags(trim($data['startDate']));
        $endDate = strip_tags(trim($data['endDate']));

        if(!$keyword&&!$startDate&&!$endDate){
            $sql = 'select id,name,source from documents where delete_flag = 0 order by created_at desc limit 5';
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo  json_encode($data);
            exit;
        }
       
       $startDate = date('Y-m-d H:i:s',strtotime(strip_tags(trim($data['startDate']))));//用strtotime轉換日期文字格式為unix timestamp
       if(!$endDate){
        $endDate = date("Y-m-d H:i:s");  
       }else{
        $endDate = date('Y-m-d H:i:s',strtotime(strip_tags(trim($data['endDate']))));//用strtotime轉換日期文字格式為unix timestamp
       }
       $sql = 'select id,name,source from documents where delete_flag = 0 and name like ? and created_at between ? and ? order by created_at desc';
       $stmt = $this->conn->prepare($sql);
       $stmt->execute(['%'.$keyword.'%',$startDate,$endDate]);
       $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
       echo  json_encode($data);
       exit;
    }
   
   
}

$file = new File($conn);

$file->getDataFromFrontend();

$file = null;

