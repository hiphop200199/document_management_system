<?php
require_once(__DIR__.'/parts/db.php');
class File
{
    private $conn;
    
    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }
    public function createItem() 
    {
        $target_dirctory = 'uploads/';
        $target_file = $target_dirctory. basename($_FILES['image_source']['name']);//取得檔名部分
        $image_source = $_FILES['image_source'];
        $name = strip_tags(trim($_POST['name']));
        $price = intval(strip_tags(trim($_POST['price']))) ;
        $image_file_type = strtolower(pathinfo($image_source['name'], PATHINFO_EXTENSION)) ;//用來分析路徑的資訊
        if(file_exists($target_file)){//檢查檔案是否存在
            echo json_encode(['message'=>'file already exist.']);
            exit;
        }
        if(!getimagesize($image_source['tmp_name'])){//檢查是不是圖片
            echo json_encode(['message' => 'this is not an image.']) ;
            exit;
        }
        if($image_file_type!=='png' && $image_file_type!== 'jpeg' && $image_file_type!== 'jpg'){//檢查檔案格式
            echo json_encode(['message' => 'jpeg,png only.']);
            exit;
        }
        if($image_source['size']>5000000){//檢查檔案大小
            echo json_encode(['message'=> 'file is too large.']);
            exit;
        }
        if(move_uploaded_file($image_source['tmp_name'], $target_file)){//檢查是不是php正常上傳機制上傳的檔案，如果是就移動到指定的路徑
            $theme = strip_tags(trim($_POST['theme']));
            $language = strip_tags(trim($_POST['language']));
            $author = strip_tags(trim($_POST['author']));
            $publisher = strip_tags(trim($_POST['publisher']));
            $published_date = date('Y-m-d', strtotime(strip_tags(trim($_POST['published_date']))));//轉成unix timestamp再轉成日期格式
            $introduction = strip_tags(trim($_POST['introduction']));
            $stock =intval(strip_tags(trim($_POST['stock']))) ;
            $create_sql = 'insert into  products values(?,?,?,?,?,?,?,?,?,?,?,?,?);';          
            $statement = $this->conn->prepare($create_sql);
            $statement->execute([null,$name,$price,$target_file,$theme,$language,$author,$publisher,$published_date,$introduction,$stock,null,null]);
            if ($statement->rowCount() > 0) {
                echo json_encode(['message' => 'create success', 'redirect' => 'list.php?page=1']);
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
    
   
    public function deleteItem()
    {
        $item_id = isset($_POST['itemID'])? intval($_POST['itemID']):null;//用isset確認變數是否存在
        $delete_sql = 'delete from products where id = ? ';
        $statement = $this->conn->prepare($delete_sql);
        $statement->execute([$item_id]);
        if ($statement->rowCount() > 0) {
            echo json_encode(['message' => 'delete success', 'redirect' => 'list.php?page=1']);
            exit;
        }
    }
    public function getItems()
    {
        $keyword = isset($_GET['keyword'])?strip_tags(trim($_GET['keyword'])):null;
        $page_now = isset($_GET['page'])? intval($_GET['page']):null; //從網址列參數索取當下頁面並轉數字
        
        $total_row_count_sql = $keyword===null? 'select count(*) from products':"select count(*) from products where (name like ? or introduction like ?)";
        $statement = $this->conn->prepare($total_row_count_sql);
        if($keyword===null){
            $statement->execute();
        }else{
            $statement->execute(['%'.$keyword.'%','%'.$keyword.'%']);//like句型一樣擺問號在樣板，執行時放入'%*%'即可
        }
       
        $total_row_count = $statement->fetch(PDO::FETCH_NUM)[0];
        $per_page = 10;
        $pages = intval(ceil($total_row_count / $per_page)); //無條件進位但回傳浮點數，round四捨五入，floor無條件捨去
        $data_sql = $keyword===null? sprintf('select id,name from products limit %d offset %d', $per_page, ($page_now - 1) * $per_page):sprintf('select id,name from products where (name like ? or introduction like ?) limit %d offset %d', $per_page, ($page_now - 1) * $per_page); //特定格式組字串，用offset計算起始位置，搭配limit設定要讀取的範圍
        $statement = $this->conn->prepare($data_sql);
        if($keyword===null){
            $statement->execute();
        }else{
            $statement->execute(['%'.$keyword.'%','%'.$keyword.'%']);
        }
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        if($keyword===null){
            return ['page_now' => $page_now, 'pages' => $pages, 'data' => $data];
        }else{
            return ['page_now' => $page_now, 'pages' => $pages, 'data' => $data,'keyword'=>$keyword];
        }
       
    }
   
   
}

$product = new File($conn);



