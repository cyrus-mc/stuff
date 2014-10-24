<?php

/* 
   check that the data directory is write-able (relative to current directory) 
   check that GD2 support is built into PHP
*/
function requirement_checker(){

	if(!is_writable('data/')){ errorMsg('Oh no! You have make a boo boo...! Please make sure the "data" directory is writable. Click <a href="http://ifoto.ireans.com/faq" target="_blank">here</a> for more info.'); }

	if(function_exists("gd_info")){ $info=gd_info();
		if(!strstr($info['GD Version'],"2.")){errorMsg('Please make sure you PHP compiled with GD2. Click <a href="http://ifoto.ireans.com/faq" target="_blank">here</a> for more information.');}
 	}else{
 		errorMsg('Please make sure you PHP compiled with GD2. Click <a href="http://ifoto.ireans.com/faq" target="_blank">here</a> for more information.');
 	}

}

/* delete files from directory */
function ClearDirectory($path){
   if($dir_handle = opendir($path)){
       while($file = readdir($dir_handle)){
           if($file == "." || $file == ".."){
               if(!@unlink($path."/".$file)){
                   continue;
               }
           }else{
               @unlink($path."/".$file);
           }
       }
       closedir($dir_handle);
       return true;
// all files deleted
   }else{
       return false;
// directory doesn?t exist
   }
}

/* remove directory, after clearing all files */
function RemoveDirectory($path){
   if(ClearDirectory($path)){
       if(rmdir($path)){
           return true;
// directory removed
       }else{
           return false;
// directory couldn?t removed
       }
   }else{
       return false;
// no empty directory
   }
}

function directoryToArray($directory, $recursive) {
	$array_items = array();
	$array_items = glob($directory . '/*', GLOB_ONLYDIR);
	return $array_items;
}

function multi_array_sort($array='', $key='', $order='asc')
{
	if($array AND $key){

		foreach ($array as $i => $k) {
			$sort_values[$i] = $array[$i][$key];
		}
		if($order == 'desc'){
			rsort ($sort_values);
		}else{
			asort ($sort_values);
		}
		reset ($sort_values);
		while (list ($arr_key, $arr_val) = each ($sort_values)) {
			$sorted_arr[] = $array[$arr_key];
		}
		return $sorted_arr;

	}else{
		return false;
	}

}

function multi_array_search($search_value, $the_array)
{
   if (is_array($the_array))
   {
       foreach ($the_array as $key => $value)
       {
           $result = multi_array_search($search_value, $value);
           if (is_array($result))
           {
               $return = $result;
               array_unshift($return, $key);
               return $return;
           }
           elseif ($result == true)
           {
               $return[] = $key;
               return $return;
           }
       }
       return false;
   }
   else
   {
       if ($search_value == $the_array)
       {
           return true;
       }
       else return false;
   }
}

/* for debug purpose */
function pre($sting_to_pre = ''){

    if($sting_to_pre){
        echo '<pre>';
        print_r($sting_to_pre);
        echo '</pre>';
    }
}

?>
