<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopicRequest;
use App\Models\Category;
use Auth;
use App\Handlers\ImageUploadHandler;
use App\Models\User;

class TopicsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

	public function index(Request $request,Topic $topic,User $user)
	{
        $topics = $topic->withOrder($request->order)
                        ->with('user','category')  //预加载防止N+1
                        ->paginate(30);
        $active_users = $user->getActiveUsers();
        // dd($active_users);
		return view('topics.index', compact('topics','active_users'));
	}

    public function show(Request $request,Topic $topic)
    {
        //url矫正，如果帖子slug不为空，并且请求带着的slug与帖子slug不一致，
        // 则当条件允许的时候，我们将发送 301 永久重定向到正确的url：(附带正确slug的链接)
        if(!empty($topic->slug) && $topic->slug !=$request->slug){
            return redirect($topic->link(),301);
        }
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

		return redirect()->to($topic->link())->with('success', '帖子创建成功！');
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

		return redirect()->to($topic->link())->with('success', '更新成功！');
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
