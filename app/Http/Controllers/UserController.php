<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Phone;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $output_user=[];
            $user_list=[];
            $data=User::select('id', 'created_at','updated_at','email','fullname')->orderBy('fullname','asc')->get();
            if(count($data))
            {
                foreach($data as $row)
                {
                    $user_list[]=$row->id;
                    $output_user[]=[
                        'id'=>(int) $row->id,
                        'created_at'=>(string) date('Y-m-d H:i:s',strtotime($row->created_at)),
                        'updated_at' => (string) date('Y-m-d H:i:s', strtotime($row->updated_at)),
                        'email'=> (string) $row->email,
                        'fullname'=> (string) $row->fullname
                    ];
                }
            }

            $output_reference=[];
            $with=$request->with;
            if(!empty($with) && count($data))
            {
                $reference_data=Phone::whereIn('user_id',$user_list)->select($with)->get();
                if(count($reference_data))
                {
                    foreach($reference_data as $rd)
                    {
                        $output_reference[$rd->user_id]=$rd;
                    }
                }
            }

            $output_final=[];
            if(count($data))
            {
                foreach($output_user as $ou)
                {
                    $reference=isset($output_reference[$ou['id']])? $output_reference[$ou['id']]:[];
                    $item=$ou;
                    $item['reference']=$reference;
                    $output_final[]=$item;
                }
            }

            return response()->json([
                'status'=>'success',
                'data'=>[
                    'user'=> $output_final
                ],
                'message'=>'Getting Data'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>'failed',
                'data'=>[],
                'message'=>$th->getMessage().' '.$th->getFile().' '.$th->getFile()
            ]);
        }
    }

    public function view(Request $request, string $user)
    {
        try {
            $output_user = [];
            $data = User::select('id','created_at', 'updated_at', 'email', 'fullname')
            ->where('id',$user)->first();
            if (!empty($data->id)) {
                $output_user= [
                    'id' => (int) $data->id,
                    'created_at' => (string) date('Y-m-d H:i:s', strtotime($data->created_at)),
                    'updated_at' => (string) date('Y-m-d H:i:s', strtotime($data->updated_at)),
                    'email' => (string) $data->email,
                    'fullname' => (string) $data->fullname
                ];
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'user' => $output_user
                ],
                'message' => 'Getting Data'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'data' => [],
                'message' => $th->getMessage() . ' ' . $th->getFile() . ' ' . $th->getFile()
            ]);
        }
    }
}
