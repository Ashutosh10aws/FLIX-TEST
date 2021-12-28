@extends('admin.layouts.template',['menu'=>'remove-device'])

@section('css')

@endsection

@section('page-content')
    <div class="page-content">
        <div class="panel panel-boxed">
            <div class="panel-body">
                <form method="post" action="{{url('admin/postRemoveDevice')}}">
                    @csrf
                    <div class="remove-device-item">
                        <span class="remove-device-item-label">Total Device Count: </span>
                        <span>{{$total_device_count}}</span>
                    </div>
                    <div class="remove-device-item">
                        <span class="remove-device-item-label">Old Device Count: </span>
                        <span>{{$old_device_count}}</span>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-danger">Remove Devices</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

