@extends('layouts.admin')

@section('title', 'This is a blank page')
@section('description', 'This is a blank page that needs to be implemented')

@section('content')
<script src="../../plugins/jQuery/jquery-2.2.3.min.js"></script>
<section class="content">
<script type="text/javascript">

  function test(){
   $("#myModal").modal("show");
   }
   function testPhanquyen(){

    $("#myModalPhanQuyen").modal("show");
   }
</script>
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-md" style="width: auto;margin: 10px;">
    
      <!-- Modal content-->
      <div class="modal-content box">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Thêm mới khối</h4>
        </div>
        <form class="form-horizontal" action="">  
        <input type="hidden" class="form-control" id="txtIdRoleGroup">          
                <div class="modal-body">
                    <div class="row" id="group_message" style="padding-left: 10%;padding-right: 10%"></div>   
                    <div class="box-body">
                <div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Mã khối</label>

                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="txtRoleCode" placeholder="Nhập mã nhóm">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Tên khối</label>

                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="txtRoleName" placeholder="Nhập tên nhóm">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Trạng thái</label>

                  <div class="col-sm-10">
                   
                  </div>
                </div>
              </div>   
                </div>
                <div class="modal-footer">
                    <div class="row text-center">
                        <button type="button" data-loading-text="Đang thêm mới dữ liệu" class="btn btn-primary" id ="updateRole">Thêm mới</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </form>   
      </div>
      
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
       <b> Danh mục </b> / Khối
        <a data-toggle="modal" data-target="#myModal" style="margin-left: 10px" class="btn btn-success btn-xs pull-right"  >
            <i class="glyphicon glyphicon-plus"></i> Tạo mới
        </a > 
        <a class="btn btn-success btn-xs pull-right"  href="#">
            <i class="glyphicon glyphicon-print"></i> Xuất excel
        </a>
    </div>
    </div>
          <div class="box">
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-striped table-bordered table-hover dataTable no-footer">
                <thead>
                <tr>
                  <th class="text-center" style="vertical-align:middle">STT</th>
                  <th class="text-center" style="vertical-align:middle">Báo cáo</th>
                  <th class="text-center" style="vertical-align:middle">Mô tả</th>
                  <th class="text-center" style="vertical-align:middle">Nghị định</th>
                </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>1</td>
                    <td>Miễn giảm học phí</td>
                    <td>Nhu cầu kinh phí năm (n) và dự toán năm (n+1) đối với chính sách miễn, giảm học phí 
</td>
                    <td>Theo nghị định số 86/2015/NĐ-CP của chính phủ</td>
                    <td> <a class="btn btn-success btn-xs pull-right"  href="#">
            <i class="glyphicon glyphicon-print"></i> Báo cáo
        </a></td>
                  </tr>
                  <tr>
                    <td>2</td>
                    <td>Hỗ trợ chi phí học tập</td>
                    <td>Nhu cầu kinh phí năm (n) và dự toán năm (n+1) đối với chính sách hỗ trợ chi phí học tập

</td>
                    <td>Theo nghị định số 86/2015/NĐ-CP của chính phủ</td>
                    <td> <a class="btn btn-success btn-xs pull-right"  href="#">
            <i class="glyphicon glyphicon-print"></i> Báo cáo
        </a></td>
                  </tr>
                  <tr>
                    <td>3</td>
                    <td>Hỗ trợ ăn trưa cho trẻ mẫu giáo</td>
                    <td>Nhu cầu kinh phí năm (n) và dự toán năm (n+1) đối với chính sách hỗ trợ ăn trưa cho trẻ em mẫu giáo
</td>
                    <td>Theo QĐ số  60/QĐ-TTG của thủ tướng chính phủ</td>
                    <td> <a class="btn btn-success btn-xs pull-right"  href="#">
            <i class="glyphicon glyphicon-print"></i> Báo cáo
        </a></td>
                  </tr>
                  <tr>
                    <td>4</td>
                    <td>Hỗ trợ học sinh bán trú</td>
                    <td>Nhu cầu kinh phí năm (n) và dự toán năm (n+1) đối với chính sách hỗ trợ học sinh bán trú
</td>
                    <td>Theo quyết định số 85/2010/QĐ-TTG của thủ tướng chính phủ</td>
                    <td> <a class="btn btn-success btn-xs pull-right"  href="#">
            <i class="glyphicon glyphicon-print"></i> Báo cáo
        </a></td>
                  </tr>
                  <tr>
                    <td>5</td>
                    <td>Hỗ trợ người nấu ăn</td>
                    <td>Nhu cầu kinh phí năm (n) và dự toán năm (n+1) đối với chính sách hỗ trợ người nấu ăn

</td>
                    <td>Theo nghị quyết số 23/2015/NQ – HĐND của Hội đồng nhân dân tỉnh Yên Bái</td>
                    <td> <a class="btn btn-success btn-xs pull-right"  href="#">
            <i class="glyphicon glyphicon-print"></i> Báo cáo
        </a></td>
                  </tr>
                  <tr>
                    <td>6</td>
                    <td>Hỗ trợ học sinh khuyết tật</td>
                    <td>Báo cáo nhu cầu kinh phí năm n và dự toán năm (n+1) – Chính sách hỗ trợ học sinh khuyết tật

</td>
                    <td>Theo thông tư liên tịch số 42/2013/TTLT-BGDĐT – BLĐTBXH – BTC – Khối mầm non và trung học phổ thông</td>
                    <td> <a class="btn btn-success btn-xs pull-right"  href="#">
            <i class="glyphicon glyphicon-print"></i> Báo cáo
        </a></td>
                  </tr>
                  <tr>
                    <td>7</td>
                    <td>Hỗ trợ học sinh dân tộc thiểu số</td>
                    <td>Báo cáo nhu cầu kinh phí năm n và dự toán năm (n+1) – Chính sách hỗ trợ học sinh dân tộc thiểu số tại huyện Mù Cang Chải và huyện Trạm Tấu

</td>
                    <td>Theo qđ số 22/2016/QĐ-UBND của UBND tỉnh Yên Bái</td>
                    <td> <a class="btn btn-success btn-xs pull-right"  href="#">
            <i class="glyphicon glyphicon-print"></i> Báo cáo
        </a></td>
                  </tr>
                  <tr>
                    <td>8</td>
                    <td>Chế độ ưu đãi</td>
                    <td>Báo cáo tổng hợp nhu cầu kinh phí năm n và dự toán năm (n+1) đối với các chế độ chính sách ưu đãi cho trẻ em mẫu giáo, học sinh, sinh viên

</td>
                    <td>-</td>
                    <td> <a class="btn btn-success btn-xs pull-right"  href="#">
            <i class="glyphicon glyphicon-print"></i> Báo cáo
        </a></td>
                  </tr>
                </tbody>
              </table>
            </div>
            
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>

@endsection