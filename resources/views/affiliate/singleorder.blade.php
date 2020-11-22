@extends('frontend.layouts.app')

<link href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css" rel="stylesheet">

@section('content')
    <section class="gry-bg py-4 profile">
        <div class="container">
            <div class="row cols-xs-space cols-sm-space cols-md-space">
                <div class="col-lg-3 d-none d-lg-block">
                    @if(Auth::user()->user_type == 'seller')
                        @include('frontend.inc.seller_side_nav')
                    @elseif(Auth::user()->user_type == 'customer')
                        @include('frontend.inc.customer_side_nav')
                    @endif
                </div>

                <div class="col-lg-9">
                    <div class="main-content">
                        <!-- Page title -->
                        <div class="page-title">
                            <div class="row align-items-center">
                                <div class="col-md-6 col-12 d-flex align-items-center">
                                    <div id="bar-code">
                                      {!! QrCode::size(100)->generate(route('getUserSendCart', $order->token)); !!}
                                    </div>


                                </div>


                                <div class="col-md-6 col-12">
                                    <div class="float-md-right">
                                        <ul class="breadcrumb">
                                            <li><a href="{{ route('home') }}">{{ translate('Home') }}</a></li>
                                            <li><a href="{{ route('dashboard') }}">{{ translate('Dashboard') }}</a></li>
                                            <li class="active"><a href="{{ route('affiliate.user.index') }}">{{translate('Affiliate System')}}</a></li>
                                        </ul>
                                        <br>
                                    </div>
                                </div>
                            </div>

                            <div class="buttons">

                            <div class="row">
                                <div class="col-md-3">
                                    <a class="btn btn-info" id="print-code"> <i class="fa fa-print"></i> طباعه </a>
                                </div>
                                <div class="col-md-3">
                                    <a type="button" data-toggle="modal" data-target="#whatsapp"  class="btn btn-styled btn-base-1"><i class="fa fa-whatsapp"></i> {{ translate('Whats App')}}</a>

                                </div>
                                <div class="col-md-3">
                                    <a href=""  class="btn btn-warning"><i class="fa fa-mobile-phone"></i> {{ translate('SMS')}}</a>
                                </div>
                                <div class="col-md-3">
                                    <a type="button" data-toggle="modal" data-target="#GuestCheckout"  class="btn btn-danger"><i class="fa fa-envelope"></i> {{ translate('Send By Email')}}</a>
                                </div>
                            </div>
                            </div>
                        </div>


                        <div class="row">

                            <div class="col-md-4">
                                <div class="dashboard-widget text-center green-widget text-white mt-4 c-pointer">
                                    <i class="fa fa-edit"></i>
                                    <span class="d-block title heading-3 strong-400"> {{ $order->id }} </span>
                                    <span class="d-block sub-title"> كود الطلب</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="dashboard-widget text-center blue-widget text-white mt-4 c-pointer">
                                    <i class="fa fa-dollar"></i>
                                    <span class="d-block title heading-3 strong-400"> {{ $order->total }} </span>
                                    <span class="d-block sub-title"> المجموع الفعلي </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="dashboard-widget text-center green-widget text-white mt-4 c-pointer">
                                    <i class="fa fa-calendar"></i>
                                    <span class="d-block title heading-3 strong-400"> {{ date('Y-m-d', strtotime($order->created_at)) }} </span>
                                    <span class="d-block sub-title"> تاريخ الطلب</span>
                                </div>
                            </div>


                            <div class="col-md-12">

                                <h3 class="text-center text-success" style="margin-top: 50px;"> منتجات الطلب </h3>
                                <div class="card no-border mt-5">


                                    <div class="card-body">
                                        <table class="example table table-sm table-responsive-md mb-0">


                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>{{ translate('Name') }}</th>
                                                    <th>{{translate('Quantity')}}</th>
                                                    <th>{{translate('Price')}}</th>
                                                    <th>{{translate('Tax')}}</th>

                                                </tr>
                                            </thead>


                                            <tbody>

                                                @php
                                                    $products = \App\Sendcartuser::where('order_id', '=', $order->id)->get();
                                                @endphp
                                                @foreach ($products as $index=>$product)


                                                <tr>
                                                    <th>{{ $index + 1  }}</th>
                                                    <th>{{ $product->product->name}}</th>
                                                    <th>{{ $product->quantity }}</th>
                                                    <th>{{ $product->price }}</th>
                                                    <th>{{ $product->tax }}</th>
                                                 </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                </div>

                            </div>



         @php  $userorders = App\Order::where('token', '=', $order->token)->latest()->get();   @endphp

                            @foreach ($userorders as $userorder)

                            @php  $total = 0;  @endphp

  @foreach ($userorder->orderDetails as $index=>$product)
     @php
     $price = $product->price * $product->quantity;
     $total .= $price;
     @endphp
                            @endforeach






                            <div class="col-md-12">

                                <h3 class="text-center text-success" style="margin-top: 50px;">  تفاصيل الشراء </h3>
                                <div class="card no-border mt-5">

                                    <ul class="patient-data">
                                        <li>  اسم المريض<br><span> {{ $userorder->user->name  }}</span> </li>
                                        <li>  رقم الهاتف<br><span> {{ $userorder->user->phone  }} </span> </li>
                                        <li>  تاريخ الشراء<br><span> {{ date('Y M d', strtotime($userorder->created_at))}} </span> </li>
                                        <li> مجموع الفاتوره <br><span> {{ $total }} </span> </li>
                                        <li>  النسبه <br><span>
@php

$option = App\AffiliateOption::where('type', '=', 'product_sharing')->first();

$comisstion = $option->percentage;

$balance = $total / $comisstion;

@endphp



        {{ $balance }} </span> </li>
                                    </ul>


                                    <div class="card-body">

                                        <table id="example" class="example display" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>{{ translate('Name') }}</th>
                                                    <th>{{translate('Quantity')}}</th>
                                                    <th>{{translate('Price')}}</th>
                                                    <th>{{translate('Tax')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>


                                                @php  $products = \App\Sendcartuser::where('order_id', '=', $order->id)->get();  @endphp

                                                @foreach ($userorder->orderDetails as $index=>$product)

                                                 <tr>

                                                    <th>{{ $index + 1  }}</th>
                                                    <th>{{ $product->product->name}}</th>
                                                    <th>{{ $product->quantity }}</th>
                                                    <th>{{ $product->price }}</th>
                                                    <th>{{ $product->tax }}</th>
                                                </tr>

                                                @endforeach

                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>#</th>
                                                    <th>{{ translate('Name') }}</th>
                                                    <th>{{translate('Quantity')}}</th>
                                                    <th>{{translate('Price')}}</th>
                                                    <th>{{translate('Tax')}}</th>
                                                </tr>
                                            </tfoot>
                                        </table>

                                    </div>

                                </div>

                            </div>

                            @endforeach


                        </div>
                    </div>
                </div>
            </div>



        </div>
    </section>





    <div class="modal fade" id="affiliate_withdraw_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size" role="document">
            <div class="modal-content position-relative">
                <div class="modal-header">
                    <h5 class="modal-title strong-600 heading-5">{{ translate('Affiliate Withdraw Request')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="" action="{{ route('affiliate.withdraw_request.store') }}" method="post">
                    @csrf
                    <div class="modal-body gry-bg px-3 pt-3">
                        <div class="row">
                            <div class="col-md-3">
                                <label>{{ translate('Amount')}} <span class="required-star">*</span></label>
                            </div>
                            <div class="col-md-9">
                                <input type="number" class="form-control mb-3" name="amount" min="1" max="{{ Auth::user()->affiliate_user->balance }}" placeholder="{{ translate('Amount')}}" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-base-1">{{ translate('Confirm')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>

        .patient-data{

            display: flex;
            text-align: center;
        }
        .patient-data li{
            list-style: none;
            margin: 20px 30px;
            font-size: 18px;
            color: #28a745;
        }
        .patient-data li span{
            color: #000
        }

        .buttons{
            margin: 20px 0px;
        }

        .buttons-html5{
            border-color: #2ecc71;
background: #2ecc71;
box-shadow: none;
border-radius: 3px;
color: #fff;
font-size: 14px;
padding: 1px 10px;
        }
    </style>


    <div class="modal fade" id="GuestCheckout" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-zoom" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">{{ translate('Send By Email')}}</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="p-3">

                        <form class="form-default" role="form" action="{{ route('sendcartbyemail') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <input type="email" name="email" class="form-control h-auto form-control-lg" placeholder="{{ translate('Friend Email')}}">
                                <input type="hidden" name="url" class="form-control h-auto form-control-lg" value="{{ route('getUserSendCart', $order->token) }}">

                            </div>

                            <div class="row align-items-center">

                                <div class="col-md-6 text-right">
                                    <button type="submit" class="btn btn-styled btn-base-1 px-4">{{ translate('Send')}}</button>
                                </div>
                            </div>
                        </form>

                    </div>



                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="whatsapp" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-zoom" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">{{ translate('Whats App')}}</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="p-3">

                        <form class="form-default" role="form" action="{{ route('sendcartbywhatsapp') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <input type="text" name="number" class="form-control h-auto form-control-lg" placeholder="Phone Number With +xx">
                                <input type="hidden" name="url" class="form-control h-auto form-control-lg" value="{{ route('getUserSendCart', $order->token) }}">
                            </div>

                            <div class="row align-items-center">

                                <div class="col-md-6 text-right">
                                    <button type="submit" class="btn btn-styled btn-base-1 px-4">{{ translate('Send')}}</button>
                                </div>
                            </div>
                        </form>

                    </div>



                </div>
            </div>
        </div>
    </div>


@endsection

@section('script')

<script>

    $("#print-code").on('click', function () {


        var divToPrint=document.getElementById('bar-code');

        var newWin=window.open('','Print-Window');

        newWin.document.open();

        newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');

        newWin.document.close();

        setTimeout(function(){newWin.close();},10);

    });

    function showCheckoutModal(){
        $('#GuestCheckout').modal();
    }
</script>


<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"> </script>

<script src="https://cdn.datatables.net/buttons/1.5.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.0/js/buttons.html5.min.js"></script>

<script>

    $(document).ready(function() {

        $('.example').DataTable( {
            dom: 'Bfrtip',
            buttons: [
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5'
            ]
        } );

    } );



</script>


@endsection


