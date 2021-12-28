@extends('admin.layouts.template',['menu'=>'mollie-setting'])

@section('css')

@endsection

@section('page-content')
    <div class="page-content">
        <div class="panel panel-boxed">
            <div class="panel-body">
                <h3 class="panel-header">Mollie Key Setting</h3>
                <form method="post" action="{{url('admin/saveMollieSetting')}}">
                    @csrf
                    <div class="form-group">
                        <label>Api Key</label>
                        <input type="text" name="api_key" value="{{$api_key}}" class="form-control">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

