@extends('layouts.app')

@section('content')
<div class="container">
  <div class="col-md-10 offset-md-1">
    <div class="card ">

      <div class="card-header">

      </div>

      <div class="card-body">
        <h2 class="">
          <i class="far fa-edit"></i>
          @if($topic->id)
            编辑话题 {{ $topic->id }}
          @else
            新建话题
           @endif
        </h2>

        <hr>

        @if($topic->id)
          <form action="{{ route('topics.update', $topic->id) }}" method="POST" accept-charset="UTF-8">
          <input type="hidden" name="_method" value="PUT">
        @else
          <form action="{{ route('topics.store') }}" method="POST" accept-charset="UTF-8">
        @endif

          @include('common.error')

          <input type="hidden" name="_token" value="{{ csrf_token() }}">


                <div class="form-group">
                	<input class="form-control" type="text" name="title" id="title-field" value="{{ old('title', $topic->title ) }}"  placeholder="请填写标题" required/>
                </div>

                <div class="form-group">
                	<select class="form-control" name="category_id"  required>
                    <option value="" hidden disabled selected>请选择分类</option>
                    @foreach ($categories as $value)
                    <option value="{{$value->id}}">{{ $value->name }}</option>
                    @endforeach
                	</select>
                </div>


                <div class="form-group">
                	<textarea name="body" id="body-field" class="form-control" rows="3" placeholder="请填写至少三个字符的内容" required>{{ old('body', $topic->body ) }}</textarea>
                </div>


          <div class="well well-sm">
            <button type="submit" class="btn btn-primary">
              <i class="far fa-save mr-2" aria-hidden="true"></i>
              保存</button>
            {{-- <a class="btn btn-link float-xs-right" href="{{ route('topics.index') }}"> <- Back</a> --}}
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@stop

@section('styles')
  <link rel="stylesheet" type="text/css" href="{{ asset('css/simditor.css') }}">
@stop

@section('scripts')
  <script type="text/javascript" src="{{ asset('js/module.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/hotkeys.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/uploader.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/simditor.js') }}"></script>

  <script>
    $(document).ready(function() {
      var editor = new Simditor({
        textarea: $('#body-field'),
        upload: {
          url: '{{ route('topics.upload_image') }}',
          params: {
            _token: '{{ csrf_token() }}'  //表单提交的参数，laravel里面post请求必须要有csrf跨站请求伪造的_token参数
          },
          fileKey: 'upload_file',   //服务器端获取图片的键值，K要大写，如$request->upload_file，设置为upload_file
          connectionCount: 3,     //最多只能同时上传3张图片
          leaveConfirm: '文件上传中，关闭此页面将取消上传'  //上传过程中，用户关闭页面时的提醒
        },
        pasteImage:true,  //设定是否支持图片黏贴上传，这里使用true开启
      });
    });
  </script>
@stop
