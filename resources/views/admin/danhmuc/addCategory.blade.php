@extends('layouts.front')

@section('title', 'This is a blank page')
@section('description', 'This is a blank page that needs to be implemented')

@section('content')
<div class="main-content">
                 



<ol class="breadcrumb">
    <li class="active">Tiện ích</li>
        <li class="active">Quản lý Điểm đo</li>
</ol>
<div class="panel panel-default">
    <div class="panel-heading">
        Danh sách điểm đo
        <a class="btn btn-success btn-xs pull-right" href="/MeasurementPoint/Add">
            <i class="glyphicon glyphicon-plus"></i>Tạo mới
        </a>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <div id="measurementPointTable_wrapper" class="dataTables_wrapper form-inline" role="grid"><div class="row"><div class="col-sm-6"><div class="dataTables_length" id="measurementPointTable_length"><label><select name="measurementPointTable_length" aria-controls="measurementPointTable" class="form-control input-sm"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option></select> hiển thị trên trang</label></div></div><div class="col-sm-6"><div id="measurementPointTable_filter" class="dataTables_filter"><label>Tìm kiếm:<input class="form-control input-sm" aria-controls="measurementPointTable" type="search"></label></div></div></div><table class="table table-striped table-bordered table-hover dataTable no-footer" id="measurementPointTable" aria-describedby="measurementPointTable_info">
                <thead>
                    <tr role="row"><th rowspan="1" colspan="1" style="width: 169px;">Mã điểm đo</th><th style="width: 184px;" rowspan="1" colspan="1">Tên điểm đo</th><th rowspan="1" colspan="1" style="width: 119px;">Thiết bị </th><th rowspan="1" colspan="1" style="width: 119px;">Số công tơ</th><th rowspan="1" colspan="1" style="width: 229px;">Đơn vị</th><th rowspan="1" colspan="1" style="width: 92px;">Trạng thái</th><th rowspan="1" colspan="1" style="width: 250px;"></th></tr>
                </thead>
                <tbody>
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                <tr class="odd">
                            <td class=" ">PD13000119889001&nbsp; </td>
                            <td class=" ">Công ty TNHH sản xuất và kinh doanh An Thái Phú&nbsp; </td>
                             <td class=" ">150000001354&nbsp; </td>
    <td class=" ">16122793&nbsp; 
                                
                            </td>
                           
                           
                            <td class=" ">CT Điện lực Hoàng Mai &nbsp; </td>
                        
                            <td class=" ">
                                            <span class="label label-success">Đang chạy</span>
                                &nbsp; 
                            </td>
                           
                            <td class="action ">
                                <a class="btn btn-info btn-xs" href="/MeasurementPoint/Edit?id=17203">
                                    <i class="glyphicon glyphicon-pencil"></i>Sửa
                                </a>
                               
                                <a class="btn btn-danger btn-xs" id="btn-updates" data="17203"><i class="glyphicon glyphicon-edit"></i>&nbsp; Cập nhật </a>
                                <a class="btn btn-danger btn-xs" id="btn-thanhly" data="17203"><i class="glyphicon glyphicon-edit"></i>&nbsp; Thanh lý </a>
                            </td>
                        </tr><tr class="even">
                            <td class=" ">PD11000038525001&nbsp; </td>
                            <td class=" ">Cty địa chất khoáng sản - Trạm 2&nbsp; </td>
                             <td class=" ">150000002639&nbsp; </td>
    <td class=" ">16121831&nbsp; 
                                
                            </td>
                           
                           
                            <td class=" ">CT Điện lực Thanh Xuân&nbsp; </td>
                        
                            <td class=" ">
                                            <span class="label label-success">Đang chạy</span>
                                &nbsp; 
                            </td>
                           
                            <td class="action ">
                                <a class="btn btn-info btn-xs" href="/MeasurementPoint/Edit?id=17202">
                                    <i class="glyphicon glyphicon-pencil"></i>Sửa
                                </a>
                               
                                <a class="btn btn-danger btn-xs" id="btn-updates" data="17202"><i class="glyphicon glyphicon-edit"></i>&nbsp; Cập nhật </a>
                                <a class="btn btn-danger btn-xs" id="btn-thanhly" data="17202"><i class="glyphicon glyphicon-edit"></i>&nbsp; Thanh lý </a>
                            </td>
                        </tr><tr class="odd">
                            <td class=" ">pd15000064882&nbsp; </td>
                            <td class=" ">Công Ty CP ĐIỆN LỰC MIỀN BẮC LỘ 477&nbsp; </td>
                             <td class=" ">150000000585&nbsp; </td>
    <td class=" ">12174826&nbsp; 
                                
                            </td>
                           
                           
                            <td class=" ">CT Điện lực Mê Linh&nbsp; </td>
                        
                            <td class=" ">
                                            <span class="label label-success">Đang chạy</span>
                                &nbsp; 
                            </td>
                           
                            <td class="action ">
                                <a class="btn btn-info btn-xs" href="/MeasurementPoint/Edit?id=17201">
                                    <i class="glyphicon glyphicon-pencil"></i>Sửa
                                </a>
                               
                                <a class="btn btn-danger btn-xs" id="btn-updates" data="17201"><i class="glyphicon glyphicon-edit"></i>&nbsp; Cập nhật </a>
                                <a class="btn btn-danger btn-xs" id="btn-thanhly" data="17201"><i class="glyphicon glyphicon-edit"></i>&nbsp; Thanh lý </a>
                            </td>
                        </tr><tr class="even">
                            <td class=" ">150000001367&nbsp; </td>
                            <td class=" ">150000001367&nbsp; </td>
                             <td class=" ">&nbsp; </td>
    <td class=" ">AMI&nbsp; 
                                
                            </td>
                           
                           
                            <td class=" ">Công ty cổ phần Sao Việt&nbsp; </td>
                        
                            <td class=" ">
                                            <span class="label label-success">Đang chạy</span>
                                &nbsp; 
                            </td>
                           
                            <td class="action ">
                                <a class="btn btn-info btn-xs" href="/MeasurementPoint/Edit?id=17200">
                                    <i class="glyphicon glyphicon-pencil"></i>Sửa
                                </a>
                               
                                <a class="btn btn-danger btn-xs" id="btn-updates" data="17200"><i class="glyphicon glyphicon-edit"></i>&nbsp; Cập nhật </a>
                                <a class="btn btn-danger btn-xs" id="btn-thanhly" data="17200"><i class="glyphicon glyphicon-edit"></i>&nbsp; Thanh lý </a>
                            </td>
                        </tr><tr class="odd">
                            <td class=" ">PD13000133692002&nbsp; </td>
                            <td class=" ">Công ty TNHH vận tải Sông Hồng - M2&nbsp; </td>
                             <td class=" ">150000001367&nbsp; </td>
    <td class=" ">15057129&nbsp; 
                                
                            </td>
                           
                           
                            <td class=" ">CT Điện lực Hoàng Mai &nbsp; </td>
                        
                            <td class=" ">
                                            <span class="label label-success">Đang chạy</span>
                                &nbsp; 
                            </td>
                           
                            <td class="action ">
                                <a class="btn btn-info btn-xs" href="/MeasurementPoint/Edit?id=17199">
                                    <i class="glyphicon glyphicon-pencil"></i>Sửa
                                </a>
                               
                                <a class="btn btn-danger btn-xs" id="btn-updates" data="17199"><i class="glyphicon glyphicon-edit"></i>&nbsp; Cập nhật </a>
                                <a class="btn btn-danger btn-xs" id="btn-thanhly" data="17199"><i class="glyphicon glyphicon-edit"></i>&nbsp; Thanh lý </a>
                            </td>
                        </tr><tr class="even">
                            <td class=" ">PD13000125360001&nbsp; </td>
                            <td class=" ">Cty CP sản xuất thương mại số 1 Tràng Tiền&nbsp; </td>
                             <td class=" ">150000001368&nbsp; </td>
    <td class=" ">14032724&nbsp; 
                                
                            </td>
                           
                           
                            <td class=" ">CT Điện lực Hoàng Mai &nbsp; </td>
                        
                            <td class=" ">
                                            <span class="label label-success">Đang chạy</span>
                                &nbsp; 
                            </td>
                           
                            <td class="action ">
                                <a class="btn btn-info btn-xs" href="/MeasurementPoint/Edit?id=17198">
                                    <i class="glyphicon glyphicon-pencil"></i>Sửa
                                </a>
                               
                                <a class="btn btn-danger btn-xs" id="btn-updates" data="17198"><i class="glyphicon glyphicon-edit"></i>&nbsp; Cập nhật </a>
                                <a class="btn btn-danger btn-xs" id="btn-thanhly" data="17198"><i class="glyphicon glyphicon-edit"></i>&nbsp; Thanh lý </a>
                            </td>
                        </tr><tr class="odd">
                            <td class=" ">150000000617&nbsp; </td>
                            <td class=" ">150000000617&nbsp; </td>
                             <td class=" ">150000000895&nbsp; </td>
    <td class=" ">12175822&nbsp; 
                                
                            </td>
                           
                           
                            <td class=" ">Điểm đo chờ khai báo&nbsp; </td>
                        
                            <td class=" ">
                                            <span class="label label-success">Đang chạy</span>
                                &nbsp; 
                            </td>
                           
                            <td class="action ">
                                <a class="btn btn-info btn-xs" href="/MeasurementPoint/Edit?id=17197">
                                    <i class="glyphicon glyphicon-pencil"></i>Sửa
                                </a>
                               
                                <a class="btn btn-danger btn-xs" id="btn-updates" data="17197"><i class="glyphicon glyphicon-edit"></i>&nbsp; Cập nhật </a>
                                <a class="btn btn-danger btn-xs" id="btn-thanhly" data="17197"><i class="glyphicon glyphicon-edit"></i>&nbsp; Thanh lý </a>
                            </td>
                        </tr><tr class="even">
                            <td class=" ">150000000395&nbsp; </td>
                            <td class=" ">150000000395&nbsp; </td>
                             <td class=" ">150000000395&nbsp; </td>
    <td class=" ">16122866&nbsp; 
                                
                            </td>
                           
                           
                            <td class=" ">Điểm đo chờ khai báo&nbsp; </td>
                        
                            <td class=" ">
                                            <span class="label label-success">Đang chạy</span>
                                &nbsp; 
                            </td>
                           
                            <td class="action ">
                                <a class="btn btn-info btn-xs" href="/MeasurementPoint/Edit?id=17196">
                                    <i class="glyphicon glyphicon-pencil"></i>Sửa
                                </a>
                               
                                <a class="btn btn-danger btn-xs" id="btn-updates" data="17196"><i class="glyphicon glyphicon-edit"></i>&nbsp; Cập nhật </a>
                                <a class="btn btn-danger btn-xs" id="btn-thanhly" data="17196"><i class="glyphicon glyphicon-edit"></i>&nbsp; Thanh lý </a>
                            </td>
                        </tr><tr class="odd">
                            <td class=" ">PD08000113130001&nbsp; </td>
                            <td class=" ">Công ty Cổ phần nhựa Hiệp Hòa VN&nbsp; </td>
                             <td class=" ">150000001373&nbsp; </td>
    <td class=" ">15016011&nbsp; 
                                
                            </td>
                           
                           
                            <td class=" ">CT Điện lực Đông Anh&nbsp; </td>
                        
                            <td class=" ">
                                            <span class="label label-success">Đang chạy</span>
                                &nbsp; 
                            </td>
                           
                            <td class="action ">
                                <a class="btn btn-info btn-xs" href="/MeasurementPoint/Edit?id=17195">
                                    <i class="glyphicon glyphicon-pencil"></i>Sửa
                                </a>
                               
                                <a class="btn btn-danger btn-xs" id="btn-updates" data="17195"><i class="glyphicon glyphicon-edit"></i>&nbsp; Cập nhật </a>
                                <a class="btn btn-danger btn-xs" id="btn-thanhly" data="17195"><i class="glyphicon glyphicon-edit"></i>&nbsp; Thanh lý </a>
                            </td>
                        </tr><tr class="even">
                            <td class=" ">150000001375&nbsp; </td>
                            <td class=" ">150000001375&nbsp; </td>
                             <td class=" ">&nbsp; </td>
    <td class=" ">AMI&nbsp; 
                                
                            </td>
                           
                           
                            <td class=" ">Điểm đo chờ khai báo&nbsp; </td>
                        
                            <td class=" ">
                                            <span class="label label-success">Đang chạy</span>
                                &nbsp; 
                            </td>
                           
                            <td class="action ">
                                <a class="btn btn-info btn-xs" href="/MeasurementPoint/Edit?id=17194">
                                    <i class="glyphicon glyphicon-pencil"></i>Sửa
                                </a>
                               
                                <a class="btn btn-danger btn-xs" id="btn-updates" data="17194"><i class="glyphicon glyphicon-edit"></i>&nbsp; Cập nhật </a>
                                <a class="btn btn-danger btn-xs" id="btn-thanhly" data="17194"><i class="glyphicon glyphicon-edit"></i>&nbsp; Thanh lý </a>
                            </td>
                        </tr></tbody>
            </table><div class="row"><div class="col-sm-6"><div class="dataTables_info" id="measurementPointTable_info" role="alert" aria-live="polite" aria-relevant="all">Hiển thị 1 đến 10 của 1,208 bản ghi</div></div><div class="col-sm-6"><div class="dataTables_paginate paging_simple_numbers" id="measurementPointTable_paginate"><ul class="pagination"><li class="paginate_button previous disabled" aria-controls="measurementPointTable" tabindex="0" id="measurementPointTable_previous"><a href="#">Trang trước</a></li><li class="paginate_button active" aria-controls="measurementPointTable" tabindex="0"><a href="#">1</a></li><li class="paginate_button " aria-controls="measurementPointTable" tabindex="0"><a href="#">2</a></li><li class="paginate_button " aria-controls="measurementPointTable" tabindex="0"><a href="#">3</a></li><li class="paginate_button " aria-controls="measurementPointTable" tabindex="0"><a href="#">4</a></li><li class="paginate_button " aria-controls="measurementPointTable" tabindex="0"><a href="#">5</a></li><li class="paginate_button disabled" aria-controls="measurementPointTable" tabindex="0" id="measurementPointTable_ellipsis"><a href="#">…</a></li><li class="paginate_button " aria-controls="measurementPointTable" tabindex="0"><a href="#">121</a></li><li class="paginate_button next" aria-controls="measurementPointTable" tabindex="0" id="measurementPointTable_next"><a href="#">Trang tiếp</a></li></ul></div></div></div></div>
        </div>
    </div>
</div>
<div class="utility-modal modal fade" id="measurementPointUpdate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" style="width: 800px;">
     

    <div class="modal-content box">
        <div class="modal-header">
            <button type="button" class="close" style="margin-top: -10px;" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title" id="titleMeterInfo">CẬP NHẬT ĐIỂM ĐO</h4>
        </div>
        <form id="user_form" action="" class="form-horizontal">
            <div class="modal-body">
                <div class="row" id="meters_message"></div>   
                <input class="hidden" name="metersId" id="measurementPointId" type="text">
                
                <div class="row"> 
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-2 control-label" style="text-align:left">Mã thiết bị</label>
                            <label class="col-sm-8 control-label" id="modem" style="text-align:left"></label>
                        </div>
                    </div>
                    
                </div><!----------------------->    

                <div class="row"> <!----------------------->
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-2 control-label" style="text-align:left">Số công tơ</label>
                            <label class="col-sm-8 control-label" id="meter" style="text-align:left"></label>
                        </div>
                    </div> 
                </div><!----------------------->    
               <div class="row"> <!----------------------->
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-2 control-label" style="text-align:left">Mã điểm đo</label>
                            <div class="col-sm-7">                             
                                <input id="measurementPointCode" name="measurementPointCode" class="form-control" type="text">
                            </div>
<div class="col-sm-3">                             
                             <button type="button" data-loading-text="Đang tìm kiếm" class="btn btn-primary" id="btn-SearchMP">Tìm kiếm</button>
                            </div>
                        </div>
                    </div> 
                </div><!----------------------->    

                    
                       <div class="row" style="overflow: auto; height: 350px">
<div class="col-sm-12">
                        <table class="table table-striped table-bordered" style="width: 100%">                                   
                            <thead>
                                <tr class="success">
                                    <th class="text-center">STT</th>
                                    <th class="text-center">Khách hàng<br><span></span></th>
                                    <th class="text-center">Số công tơ<br><span></span></th>
                                    <th class="text-center">Mã thiết bị<br><span></span></th>                                  
                                    <th class="text-center">Dữ liệu gần nhất<br><span></span></th>                                  
                                    <th class="text-center">Chức năng<br><span></span></th>                                  
                                </tr>                           
                            </thead>
                            <tbody id="listMeasurementPointBody">
</tbody>
                        </table>
<div class="box-footer clearfix">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="text-right col-md-6 control-label">Tổng  </label>
                                <label class="col-md-6 control-label g_countRowsPaging">0</label>
                            </div>
                            <div class="col-md-4">
                                <label class="col-md-3 control-label text-right">Trang </label>
                                <div class="col-md-7">
                                    <select class="form-control-static g_selectPaging">
                                        <option value="0"> 0 / 20 </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <ul class="pagination pagination-sm no-margin pull-right g_clickedPaging">
                                    <li><a>«</a></li>
                                    <li><a>0</a></li>
                                    <li><a>»</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    </div>
                    </div> 
                </div><!----------------------->    
                     <div class="modal-footer">
                <div class="row text-center">
                    <!--<% if (check_Permission_Feature ('TK_DTK_CONG_TO_UPDATE')) { %>-->
                    
                    <!--<% } %>-->
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Đóng</button>
                </div>
            </div>    

        </form>           
    </div>   
    </div>
</div>

            </div>
@endsection