<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusinessAdminController extends Controller
{
    public function index(Request $request)
    {
        $q  = trim((string)$request->query('q',''));
        $cid = $request->query('cid');
        $sid = $request->query('sid');

        $rows = DB::table('business as b')
            ->leftJoin('category as c','c.cat_id','=','b.cid')
            ->leftJoin('city as ci','ci.city_id','=','b.sid')
            ->select('b.*','c.category as category_name','ci.city as city_name')
            ->when($q !== '', function($qb) use ($q) {
                $qb->where(function($w) use ($q){
                    $w->where('b.business_name','like',"%$q%")
                      ->orWhere('b.description','like',"%$q%");
                });
            })
            ->when(!empty($cid), fn($qb) => $qb->where('b.cid',$cid))
            ->when(!empty($sid), fn($qb) => $qb->where('b.sid',$sid))
            ->orderBy('b.biz_id','desc')
            ->paginate(15)
            ->withQueryString();

        $cats = DB::table('category')->orderBy('category')->get();
        $cities = DB::table('city')->orderBy('city')->get();

        return view('admin.businesses.index', compact('rows','q','cid','sid','cats','cities'));
    }

    public function edit(int $id)
    {
        $row = DB::table('business')->where('biz_id',$id)->first();
        abort_if(!$row, 404);
        $cats = DB::table('category')->orderBy('category')->get();
        $cities = DB::table('city')->orderBy('city')->get();
        return view('admin.businesses.edit', compact('row','cats','cities'));
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'business_name' => ['required','string','max:255'],
            'description'   => ['nullable','string'],
            'cid'           => ['required','integer'],
            'sid'           => ['required','integer'],
            'menu'          => ['nullable','integer'],
        ]);

        if (!isset($data['menu'])) $data['menu'] = 0; // default legado

        DB::table('business')->where('biz_id',$id)->update($data);
        return redirect()->route('admin.businesses.index')->with('success','Negócio atualizado.');
    }

    public function destroy(int $id)
    {
        DB::table('business')->where('biz_id',$id)->delete();
        return redirect()->route('admin.businesses.index')->with('success','Negócio removido.');
    }
}