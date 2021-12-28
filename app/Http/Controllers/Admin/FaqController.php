<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Faq;
use App\Traits\SummernoteOperation;

class FaqController extends Controller
{
    use SummernoteOperation;
    public function index(){
        $faq=null;
        if(Faq::get()->count()>0)
            $faq=Faq::get()->first();
        return view('admin.faqs.index',compact('faq'));
    }

    public function save(Request $request){
        $this->validate($request, [
            'section-content' => 'required',
        ]);
        $detail=$request->input('section-content');
        $detail=$this->changeDomContent($detail);

        if(Faq::get()->count()==0)
            $summernote = new Faq;
        else
            $summernote =Faq::get()->first();
        $summernote->contents= $detail;
        $summernote->save();
        return redirect()->back();
    }
}
