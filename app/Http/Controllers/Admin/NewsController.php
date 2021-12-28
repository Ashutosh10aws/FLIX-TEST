<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\News;
use Illuminate\Http\Request;
use App\Traits\SummernoteOperation;

class NewsController extends Controller
{
    use SummernoteOperation;
    public function index(){
        $news_sections=News::get();
        return view('admin.news.index', compact('news_sections'));
    }
    public function createNewsSection($id=null){
        $section=null;
        if(!is_null($id))
            $section=News::find($id);
        return view('admin.news.create', compact('id','section'));

    }
    public function saveNewsSection(Request $request){
        $this->validate($request, [
            'section-name' => 'required',
            'section-content' => 'required',
        ]);
        $detail=$request->input('section-content');
        $section_name=$request->input('section-name');
        $section_status=$request->input('section_status');
        $id=$request->input('id');
        $detail=$this->changeDomContent($detail);
        if(!is_null($id))
            $summernote=News::find($id);
        else
            $summernote = new News;
        $summernote->contents= $detail;
        $summernote->section_name=$section_name;
        $summernote->status=$section_status;
        $summernote->save();
        return redirect('admin/news');
    }
    public function deleteNewsSection(Request $request, $id){
        News::destroy($id);
        return [
            'status'=>'success'
        ];

    }
}
