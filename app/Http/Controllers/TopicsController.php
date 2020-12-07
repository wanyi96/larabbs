<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopicRequest;
use App\Models\Category;
use Auth;
use App\Handlers\ImageUploadHandler;

class TopicsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

	public function index(Request $request,Topic $topic)
	{
        $topics = $topic->withOrder($request->order)
                        ->with('user','category')  //预加载防止N+1
                        ->paginate(30);
		return view('topics.index', compact('topics'));
	}

    public function show(Topic $topic)
    {
        return view('topics.show', compact('topic'));
    }

	public function create(Topic $topic)
	{
        $categories = Category::all();
		return view('topics.create_and_edit', compact('topic','categories'));
	}

	public function store(TopicRequest $request,Topic $topic)
	{
        //store()方法的第二个参数会获得一个空白的topic实例
        $topic->fill($request->all());  //请求表单所有数据填充到topic模型的属性中
        $topic->user_id = Auth::id();
        $topic->save();

		return redirect()->route('topics.show', $topic->id)->with('success', '帖子创建成功！');
	}

	public function edit(Topic $topic)
	{
        $this->authorize('update', $topic);  //授权验证
        $categories = Category::all();
		return view('topics.create_and_edit', compact('topic','categories'));
	}

	public function update(TopicRequest $request, Topic $topic)
	{
		$this->authorize('update', $topic);
		$topic->update($request->all());

		return redirect()->route('topics.show', $topic->id)->with('success', '更新成功！');
	}

	public function destroy(Topic $topic)
	{
		$this->authorize('destroy', $topic);
		$topic->delete();

		return redirect()->route('topics.index')->with('success', '成功删除！');
    }

    public function uploadImage(Request $request,ImageUploadHandler $uploader)
    {
        //此时，该方法第二个参数会创建一个空的upload对象实例，方便调用该类中的方法
        //初始化返回数据，默认是失败的
        $data = [
            'success' => false,
            'msg'     => '上传失败！',
            'file_path' => ''
        ];
        //判断是否有上传文件，并赋值给$file
        if($file = $request->upload_file){
            //保存图片到本地
            $result = $uploader->save($file,'topics',\Auth::id(),1024);
            //图片保存成功的话
            if($result){
                $data['file_path'] = $result['path'];
                $data['msg'] = '上传成功！';
                $data['success'] = true;
            }
        }
        return $data;
    }
}
