@extends('frontend.layouts.app')

@section('content')

    <div id="page-content">
        <section class="slice-xs sct-color-2 border-bottom">
            <div class="container container-sm">

            </div>
        </section>
        <section class="py-3 gry-bg">
            <div class="container">
                <div class="row cols-xs-space cols-sm-space cols-md-space">
                    <div class="col-lg-8">
                        <form action="{{ route('payment.checkout') }}" class="form-default" data-toggle="validator" role="form" method="POST" id="checkout-form">
                            @csrf
                            <div class="card">
                                <div class="card-title px-4 py-3">
                                    <h3 class="heading heading-5 strong-500">
                                        {{ translate('Select Send  option')}}
                                    </h3>
                                </div>
                                <div class="card-body text-center">
                                    <div class="row">



             <div class="col-md-4">

     <a type="button" data-toggle="modal" data-target="#GuestCheckout"  class="btn btn-styled btn-base-1">{{ translate('Send By Email')}}</a>
      </div>
      <div class="col-md-4">
        <a type="button" data-toggle="modal" data-target="#whatsapp"  class="btn btn-styled btn-base-1">{{ translate('Whats App')}}</a>
     </div>

         <div class="col-md-4" >

            <a href=""  class="btn btn-styled btn-base-1">{{ translate('SMS')}}</a>
        </div>



             <div class="col-md-6">
                {!! QrCode::size(100)->generate(route('getUserSendCart', $sendorder->token)); !!}
             </div>

             <div class="link-copy" style="padding: 40px 0;">
                 <div class="row">
             <div class="col-md-12">
                    <p class="lead"> {{ route('getUserSendCart', $sendorder->token) }} </p>
             </div>
             <div class="col-md-3">
                    <input style="display: none;" type="text" value="{{ route('getUserSendCart', $sendorder->token) }}" id="myInput">



  <!--<button type=button   class="btn btn-base-1" data-attrcpy="{{translate('Copied')}}" onclick="myFunction()" >{{translate('Copy Url')}}</button>-->
                    </div>
             </div>
             </div>

                                    </div>





                                </div>
                                </div>

                                </div>


                        </form>
                    </div>

                    {{-- <div class="col-lg-4 ml-lg-auto">
                        @include('frontend.partials.cart_summary')
                    </div> --}}
                </div>
            </div>
        </section>
    </div>


    {{-- // Send By Email Modal --}}

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
                                <input type="hidden" name="url" class="form-control h-auto form-control-lg" value="{{ route('getUserSendCart', $sendorder->token) }}">
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
                                <input type="hidden" name="url" class="form-control h-auto form-control-lg" value="{{ route('getUserSendCart', $sendorder->token) }}">
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

        function showCheckoutModal(){
            $('#GuestCheckout').modal();
        }

        function myFunction() {
            /* Get the text field */
            var copyText = document.getElementById("myInput");

            /* Select the text field */
            copyText.select();
            copyText.setSelectionRange(0, 99999); /*For mobile devices*/

            /* Copy the text inside the text field */
            document.execCommand("copy");

            /* Alert the copied text */
            alert("Copied the text: " + copyText.value);
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
