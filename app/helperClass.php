<?php
namespace helperClass;
class helperClass{
    public static function category($data,$parent=0,$str="",$selected=0){
        foreach ($data as $key => $value){
            if($value->parent == $parent){
                if ($selected != 0 && $value->id == $selected) {
                    echo "<option value='$value->id' selected>$str$value->title</option>";
                } else {
                    echo "<option value='$value->id'>$str$value->title</option>";
                }
                helperClass::category($data,$value->id,$str."--||",$selected);
            }
        }
    }
    public static function menu($menu,$parent=0){
        $result =null;
        $result = null;
        foreach ($menu as $item)

            if ($item->parent == $parent) {
                $item_json = json_encode($item);
                $result .= "<li class='dd-item nested-list-item' data-id='{$item->id}' data-pid='{$item->idmenu}'>
      <div class='dd-handle nested-list-handle'></div>
      <div class='nested-list-content'>{$item->title}
        <span class='tip-msg'></span>
        <div class='pull-right'><span class='tip-hide'></span>
            <a  class='delete_toggle' rel='{$item->id}' style='cursor: pointer;'>Delete</a>
        </div>
      </div>" . helperClass::menu($menu, $item->id) . "</li>";
            }
        return $result ?  "\n<ol class=\"dd-list\">\n$result</ol>\n" : null;
    }
    public static function menuIndex($data,$class="sub-nav",$parent =0,$classDown="nav",$path,$id){
       
        foreach ($data as $value){
            if($value->module_parentid == $parent){
              //  $category = route('user.home.category',$value->idmenu);
              //  $page = route('user.home.news',$value->idmenu);
              if($path == $value->module_path)
                echo "<li class='dropdown active'>";
              else{
                echo "<li class='dropdown'>";
              }
               // <li class="sub-nav-active">
                        // <a id="ctl00_rpTabMenu_ctl00_lbTabView" title="Quản lý các vấn đề về văn bản" href="javascript:__doPostBack('ctl00$rpTabMenu$ctl00$lbTabView','')">Hồ sơ , chính sách</a>
                  //  </li>
               // if($value->slug == 'categories'){

                
                $datachild = \Illuminate\Support\Facades\DB::select('select qlhs_modules.* from qlhs_modules LEFT JOIN (SELECT module_id,role_user_id from permission_users GROUP BY module_id,role_user_id ) as permission_user
 on qlhs_modules.module_id = permission_user.module_id  where module_view = :view and role_user_id = :id and module_parentid = :parent order by module_order,module_id', ['id' => $id,'view' => 1,'parent' =>$value->module_id]);
                if(count($datachild) > 0){
                  echo "<a class='dropdown-toggle' data-toggle='dropdown' href='$value->module_path'><img alt='' src='/images/$value->module_icon' class='menu_Icon' height='20px' width='20px'> $value->module_nav <span class='caret'></span></a>";
                  echo '<ul class="dropdown-menu" role="menu">';
                  foreach ($datachild as $row){
                      //if($value->module_id == $row->module_parentid){
                          echo ' <li><a href="/'.$row->module_path.'"  ><i class="fa fa-circle-o"></i> '.$row->module_name.'</a></li>';
                     // }
                  }
                  echo "   </ul>";
                }else{
                  echo "<a  href='/$value->module_path'><img alt='' src='/images/$value->module_icon' class='menu_Icon' height='20px' width='20px'> $value->module_nav</a>";
                }

                echo "</li>";

            }
            
        }

    }
    public static function menuleft($data,$class="sub-nav",$parent =0,$classDown="nav",$path="",$id){
      //var_dump($path) or die;border-collapse:collapse;" border="0" cellspacing="0"

      foreach ($data as $value){
            if($value->module_parentid == $parent){
        echo "<li class='treeview active'>
          <a  href='#'>
            <img alt='$path' src='images/$value->module_icon' class='menu_Icon' height='20px' width='20px'> <span>$value->module_name</span>
            <span class='pull-right-container'>
              <i class='fa fa-angle-left pull-right'></i>
            </span>
          </a>
          <ul class='treeview-menu'>";
             //   if($value->module_path == $path){
              // echo "  <a id='subnav_$value->module_id' class='active'></a></div>
              //   <div style='border-collapse:collapse;' border='0' cellspacing='0'' class='group_menu_left_content'> " ;
              //   }else{
              //     echo "  <a id='subnav_$value->module_id' class='hidden'></a></div>
              //   <div style='display:none;'' class='group_menu_left_content'> " ;
              //   }
          $datachild = \Illuminate\Support\Facades\DB::select('select qlhs_modules.* from qlhs_modules LEFT JOIN (SELECT module_id,role_user_id from permission_users GROUP BY module_id,role_user_id ) as permission_user
 on qlhs_modules.module_id = permission_user.module_id  where module_view = :view and role_user_id = :id and module_parentid = :parent order by module_order,module_id', ['id' => $id,'view' => 1,'parent' =>$value->module_id]);
              helperClass::menuleftchild($datachild,$class="menus",$value->module_id,"treeview-menu");
                
        echo " </ul>  </li>";
          }
      }

    }
    public static function menuleftchild($data,$class="sub-nav",$parent =0,$classDown="nav"){
     
      foreach ($data as $value){

            if($value->module_parentid == $parent){
             // $category1 = route('admin.hethong.user.listUsers');
             // $category = route('admin.hethong.group.listRoleGroup');
             echo "<li  id='document'><a href='$value->module_path' target='mainFrame' rel='$value->module_path'><i class='fa fa-circle-o'></i> $value->module_name</a></li>";
        // echo  "                   <tr class='row_item'>
        //                         <td style='width:20px;' align='center'>
        //                             <span style='padding:5px 0; display:block; border-right:dotted 1px #CCC; margin-right:5px;'>
        //                             $index
        //                             </span>
        //                         </td><td align='left'><div id='documents' >
        //                             <a href='#' rel='$value->module_path' class='number_document'><span> $value->module_name </span></a></div>
        //                         </td>
        //                     </tr> ";
    }
}

    }

    function MenuMulti($data,$parent_id ,$str='---| ',$select)
    {
      foreach ($data as $val) {
        $id = $val["site_id"];
        $ten= $val["site_name"];
        if ($val['site_parent_id'] == $parent_id) {
          if ($select!=0 && $id == $select) {
            echo '<option value="'.$id.'" selected >'.$str." ".$ten.'</option>';
          } else {
            echo '<option value="'.$id.'">'.$str." ".$ten.'</option>';
          }
          MenuMulti($data,$id,$str.'---|',$select);
        }
      }
    }
}