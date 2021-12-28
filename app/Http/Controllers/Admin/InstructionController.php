<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Instruction;
use Illuminate\Http\Request;
use App\Model\InstructionTag;
use App\Traits\SummernoteOperation;

class InstructionController extends Controller
{
    use SummernoteOperation;
    public function Tags(){
        $tags=InstructionTag::all();
        return view('admin.instructions.tags', compact('tags'));
    }
    public function createTag(Request $request){
        $tag_name=$request->input('tag_name');
        $id=$request->input('id');
        if(is_null($id) || $id=='')
            $instruction_tag=new InstructionTag;
        else
            $instruction_tag=InstructionTag::find($id);
        $instruction_tag->tag_name=$tag_name;
        $instruction_tag->save();
        return [
            'status'=>'success',
            'id'=>$instruction_tag->id
        ];
    }
    public function deleteTag(Request $request,$id){
        InstructionTag::destroy($id);
        return [
            'status'=>'success',
        ];
    }

    public function instructionTag($tag_id){
        $instruction_content=null;
        $temp=Instruction::where('tag_id',$tag_id)->get();
        if($temp->first())
            $instruction_content=$temp->first();
        return view('admin.instructions.create', compact('instruction_content','tag_id'));
    }

    public function saveInstructionPage(Request $request,$tag_id){
        $this->validate($request, [
            'section-content' => 'required',
        ]);
        $detail=$request->input('section-content');
        if(Instruction::where('tag_id',$tag_id)->get()->count()>0)
            $instruction_content=Instruction::where('tag_id',$tag_id)->get()->first();
        else
            $instruction_content=new Instruction;

        $instruction_content->tag_id=$tag_id;
        $detail=$this->changeDomContent($detail);
        $instruction_content->contents= $detail;
        $instruction_content->save();
        return redirect('/admin/instruction/tags');
    }



}
