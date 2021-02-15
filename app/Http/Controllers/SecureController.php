<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class SecureController extends Controller
{
	public function profile(Request $request)
	{
        $token = $request->bearerToken();
        try {
            $output_user = [];
            $data = User::select('id', 'created_at', 'updated_at', 'email', 'fullname')
                ->where('token', $token)->first();
            if (!empty($data->id)) {
                $output_user = [
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
