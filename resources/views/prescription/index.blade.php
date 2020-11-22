@extends('layouts.app')

@section('content')

    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">{{translate('Prescriptions')}}</h3>
        </div>
        <div class="panel-body">
            <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th width="10%">#</th>
                        <th>{{translate('User')}}</th>
                        <th>{{translate('Prescription')}}</th>
                        <th>{{translate('Date')}}</th>
                        <th>{{translate('Waiting/Done')}}</th>
                        <th>{{translate('Delete')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pres as $key => $pre)
                        <tr>
                            <td>{{ $key }}</td>
                            <td>{{ $pre->user->name }}</td>
                            <td> <a class="btn btn-primary" href="{{ asset('prescription/' . $pre->prescription) }}" target="_blank"> {{translate('View')}} </a></td>
                            <td>{{ date("Y M d", strtotime($pre->created_at))}}</td>
                            <td><label class="switch">
                                    <input onchange="update_status(this)" value="{{ $pre->id }}" type="checkbox" <?php if($pre->status == 1) echo "checked";?> >
                                    <span class="slider round"></span></label></td>
                            <td> <a href="{{ route('prescriptiondelete', $pre->id)}}" class="btn btn-danger deletebtn"> <i class="fa fa-trash-o"></i> </a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="clearfix">
                <div class="pull-right">

                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script type="text/javascript">

    function update_status(el){
        if(el.checked){
            var status = 1;
        }
        else{
            var status = 0;
        }
        $.post('{{ route('pres.status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
            if(data == 1){
                showAlert('success', 'Country status updated successfully');
            }
            else{
                showAlert('danger', 'Something went wrong');
            }
        });
    }

</script>

<script>

    $(".deletebtn").on('click', function () {

        return confirm('Are You Sure ?');

    });

</script>
@endsection
