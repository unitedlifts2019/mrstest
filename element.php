<?
    /*
        Element Framework
        Version: 1.0 RC1 Updated: 22-09-2016
        
        A MVC-Based Web Application Framework
        -Cody Joyce
        
        Element Includes
            - HTML Template Based on Bootstrap
            - jQuery
            - Complete MVC Generator Including HTML Tables and Forms based on Database Tables
            - Helper Functions to cut down on the finger gymnastics
            - Demo / Base application includes a menu and users screen to get started
    */

    define('URL',getUrl(),true);
    define('PATH',getcwd(),true);
    
    class db
    {
        static $conn;
        
        static function connect()
        {
            db::$conn = new mysqli(DB_HOST, DB_USER,DB_PASSWORD,DB_DATABASE);
        }
        
        static function query($query)
        {
            //echo $query;
            $result = mysqli_query(db::$conn,$query);
            
            if(!mysqli_error(db::$conn))
            {
                $rows = array();

                if(is_object($result)){
                    foreach($result as $row){
                        $rows[] = $row;
                    }                
                }
               
                return $rows;                
            }
        }
    }
    
    class view
    {
        function render($inc,$data = null)
        {
            if(is_array($data))
                extract($data);   
            include('public/template/header.php');
            include('html/'.$inc.'.php');
            include('public/template/footer.php');
        }
        
        function render_plain($inc,$data = null)
        {
            if(is_array($data))
                extract($data);   
            include('html/'.$inc.'.php');
        }        
    }
    
    function redirect($url)
    {
        header('Location:'.$url);
    }
    
    function geturl()
    {
        $pageURL = 'http';
        if(isset($_SERVER['HTTPS']))
                    $pageURL .= 's';
        return rtrim($pageURL.'://'.$_SERVER['HTTP_HOST'].'/'.APPDIR,"/");
    }
    
    function session_init()
    {

        session_name("changethisforeachapp");
        session_start();
        
        db::connect();
        
        if(!sess('user_id')){    
            sess('user_id','0');
            sess('user_name','guest');
            sess('user_level','0');
        }
        
        router();  
    }
    
    function router()
    {
        $secLevel = 0;
        $myClass = new login;
        $myClassName = "login";
        $myFunction = "index";
        
        $menu = db::query("select * from menu_mrs where class_name = '$myClassName'");
        
        //if user has requested a class ie USERS
        if (req('c')){  
            $myClassName = req('c');
            $menu = db::query("select * from menu_mrs where class_name = '$myClassName'"); //check if the class is in the menu system
            
            //if it is, then we set load the required security of the menu item(s)
            if(count($menu)>0){
                $secLevel = $menu[0]['auth_level'];
                $myClass = new $myClassName;
            }

            //if a function is requested
            if (req('f')){
                
                $myFunction = req('f');
                $menu = db::query("select * from menu_mrs where class_name = '$myClassName/$myFunction'");
                
                //if the function exists in the menu, load the security level.
                if(count($menu)>0){
                    $secLevel = $menu[0]['auth_level'];
                }
                
                if(!req('id')){
                    req('id','null');
                }
            }       
        }
        
        if($secLevel <= sess('user_level'))
        {
            $myClass->$myFunction();  
        }else{
            redirect(URL);
        }
    }
    
    function multimenu()
    {
        $user_level = sess('user_level');
        $items = db::query("select * from menu_mrs where auth_level <= $user_level AND menu_parent = 0 order by menu_order ASC");
        $url=URL;
        
        foreach($items as $item)
        {    
            $item_id = strtoupper($item["menu_id"]);
            $item_name = strtoupper($item["menu_name"]);
            $menu_exec = $item['class_name'];
            $menu_target = $item['menu_target'];

            $class = "";
            $kids = db::query("select * from menu_mrs where menu_parent = $item_id");
            $kidsHtml="";
            if($kids){
                $kidsHtml = "<ul>";
                foreach($kids as $kid){
                    if($kid["class_name"] == req('c')."/".req('f')){
                        $class="class='active'";
                    }                    
                    $sub_item_name = $kid["menu_name"];
                    $sub_menu_exec = $kid["class_name"];
                    $kidsHtml .= "<li $class'><a href='$url/$sub_menu_exec' target='$menu_target'>$sub_item_name</a></li>";
                }
                $kidsHtml .= "</ul>";
            }

            $class = "";
            if($item["class_name"] == req('c')){
                $class="class='active'";
            }
            
            if($item["level_only"] != 0 && $item["level_only"] == $user_level){
                echo "<li $class><a href='$url/$menu_exec' target='$menu_target'>$item_name</a>$kidsHtml</li>";
            }else{
            
            if($item["auth_level"] <= $user_level &&  $item["level_only"]== 0)
                echo "<li $class><a href='$url/$menu_exec' target='$menu_target'>$item_name</a>$kidsHtml</li>";
            }
            $class="";
        }
        
        //Pop the logout button at the end of the menu
        if($user_level > 0)
            echo "<li><a href='$url/login/logout'>Logout</a></li>";
        
    }
    
    function req($myVar,$value=null)
    {
        if(isset($value))
        {
            if($value == ""){
                unset($_REQUEST[$myVar]);
            }else{
                $_REQUEST[$myVar] = $value;
            }
        }else
        {
            if(isset($_REQUEST[$myVar])){
                $string = mysqli_real_escape_string(db::$conn,$_REQUEST[$myVar]);
				return htmlspecialchars($string);
            }else{
                return null;
            }
        }
    }
    
    function sess($myVar,$value=null)
    {
        $myVar.=session_id().$myVar;
        if(isset($value))
        {
            if($value == ""){
                unset($_SESSION[$myVar]);
            }else{
                $_SESSION[$myVar] = $value;
            }
        }else
        {
            if(isset($_SESSION[$myVar])){
                return $_SESSION[$myVar];
            }else{
                return null;
            }
        }
    }

    function loadDir($path)
    {
        $path = PATH . $path;
       
        if ($handle = opendir($path)) {
            while (false !== ($entry = readdir($handle))) {
                if(is_file($path.$entry)){
                    $ext = pathinfo($path.$entry, PATHINFO_EXTENSION);
                    if($ext == 'php'){
                        require($path."$entry");
                    }
                }
            }
            closedir($handle);
        }    
    }
    
    function checkList($table,$id,$name,$current,$where=null)
    {
        //Since the checklist uses the |id|id| method, we will modify this
        $list = db::query("select * from $table $where");
        
        $count=0;
        
        if($list){
            foreach($list as $item)
            {
                $checked = "";
                if(strstr("|".$current,$item[$id]."|"))
                    $checked = "CHECKED";
                    
                echo "<label>$item[$name]</label><input name='$id"."_".$count."' type='checkbox' class='chk_$table' $checked value='$item[$id]'><br>\n";
                $count++;
            }
        }
    } 
       
    function dropList($table,$id,$name,$current,$where=null)
    {
        $options = db::query("select * from $table $where");
        
        echo "<select name='$id' id='$id' class='form-control' required>";
        echo "<option SELECTED value=''>Please Select</option>";
        foreach($options as $option)
        {
            $selected = "";
            if($option[$id] == $current)
                $selected = "SELECTED";
                   
            echo "<option value='$option[$id]' $selected>".$option[$name].'</option>'."\n";
        }
        echo '</select>';
    }

    function getChecked($id_search)
    {
            $ids = "";
            foreach ($_REQUEST as $req_var => $req_val) {
                if(strstr($req_var,$id_search)){
                    $ids.= "|".$req_val."|";
                }
            }
            $ids = str_replace("||","|",$ids);           
            return $ids;
    }
    
    //Return a human readable date from a timestamp
    function toDate($timestamp)
    {
        if($timestamp)
        return date('d-m-Y',$timestamp);
    }
    //Return a human readable date from a timestamp
    function toTime($timestamp)
    {
        if($timestamp)
        return date('G:i:s',$timestamp);
    }    
    //Return a human readable date & time from a timestamp
    function toDateTime($timestamp,$human=null)
    {
        if($timestamp){
            if($human){
                $datestring = date("d-m-Y",$timestamp);
                $datestring .= " ";
                $datestring .= date("G:i:s",$timestamp);    
            }else{
                $datestring = date("Y-m-d",$timestamp);
                $datestring .= "T";
                $datestring .= date("G:i:s",$timestamp);  
            }
            return $datestring;
        }

    }
        
    //Returns a duration in human readable format from a timestamp in seconds.
    function toDuration($time)
    {
        if(is_numeric($time))
        {
                $value = array(
                        "years" => 0, "days" => 0, "hours" => 0,
                        "minutes" => 0, "seconds" => 0,
                );
                
                if($time >= 31556926)
                {
                        $value["years"] = floor($time/31556926);
                        $time = ($time%31556926);
                }
                if($time >= 86400)
                {
                        $value["days"] = floor($time/86400);
                        $time = ($time%86400);
                }
                if($time >= 3600)
                {
                        $value["hours"] = floor($time/3600);
                        $time = ($time%3600);
                }
                if($time >= 60)
                {
                        $value["minutes"] = floor($time/60);
                        $time = ($time%60);
                }
                $value["seconds"] = floor($time);
                //return (array) $value;
                $value["hours"] = $value["hours"] + ($value["days"] * 24);
                return $value["hours"].":".$value["minutes"].":".$value["seconds"];
        }else{
                return FALSE;
        }
    }       
?>
