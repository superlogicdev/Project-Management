<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
use App\Attachment;
use App\Comment;
use App\Task_staff_map;
use App\Task;
use App\Task_group;
use App\Project_staff_map;
use App\Payment;
use App\Project;
use App\ChatMessage;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class ClientController extends Controller
{
    //Client page load
    public function getClientPageLoad(){
        return view('backend.client');
    }
	
	//Get data for Client
    public function getClientData(Request $request){
		$search = $request->input('search');

			$data = DB::table('users')
				->join('countries', 'users.country_id', '=', 'countries.id')
				->select('users.*', 'countries.country_name')
				->where('users.role_id', 2)
				->where(function($query) use ($search){
					$query->where('name', 'LIKE', '%'.$search.'%')
						->orWhere('email', 'LIKE', '%'.$search.'%')
						->orWhere('phone', 'LIKE', '%'.$search.'%');
				})
				->orderBy('users.id', 'DESC')
				->get();

			return response()->json($data);
	}
	
	//Save data for Client
    public function saveClientData(Request $request){
		$res = array();
		
		$id = $request->input('RecordId');
		$name = $request->input('name');
		$email = $request->input('email');
		$password = $request->input('password');
		$phone = $request->input('phone');
		$skype_id = $request->input('skype_id');
		$facebook_id = $request->input('facebook_id');
		$url = $request->input('url');
		$city = $request->input('city');
		$state = $request->input('state');
		$zip_code = $request->input('zip_code');
		$country_id = $request->input('country_id');
		$address = $request->input('address');
		$active_id = $request->input('active_id');
		$photo = $request->input('photo');
		$creation_date = Carbon::now();
		
		$validator_array = array(
			'name' => $request->input('name'),
			'email' => $request->input('email'),
			'password' => $request->input('password')
		);
		$rId = $id == '' ? '' : ','.$id;
		$validator = Validator::make($validator_array, [
			'name' => 'required|max:191',
			'email' => 'required|max:191|unique:users,email' . $rId,
			'password' => 'required|max:191'
		]);

		$errors = $validator->errors();

		if($errors->has('name')){
			$res['msgType'] = 'error';
			$res['msg'] = $errors->first('name');
			return response()->json($res);
		}
		
		if($errors->has('email')){
			$res['msgType'] = 'error';
			$res['msg'] = $errors->first('email');
			return response()->json($res);
		}
		
		if($errors->has('password')){
			$res['msgType'] = 'error';
			$res['msg'] = $errors->first('password');
			return response()->json($res);
		}

		$data = array(
			'name' => $name,
			'email' => $email,
			'password' => Hash::make($password),
			'phone' => $phone,
			'skype_id' => $skype_id,
			'facebook_id' => $facebook_id,
			'url' => $url,
			'city' => $city,
			'state' => $state,
			'zip_code' => $zip_code,
			'country_id' => $country_id,
			'address' => $address,
			'active_id' => $active_id,
			'photo' => $photo,
			'role_id' => 2,
			'bactive' => base64_encode($password),
			'creation_date' => $creation_date
		);

		if($id ==''){
			$response = User::create($data);
			if($response){
				$res['msgType'] = 'success';
				$res['msg'] = __('New Data Added Successfully');
			}else{
				$res['msgType'] = 'error';
				$res['msg'] = __('Data insert failed');
			}
		}else{
			$response = User::where('id', $id)->update($data);
			if($response){
				$res['msgType'] = 'success';
				$res['msg'] = __('Data Updated Successfully');
			}else{
				$res['msgType'] = 'error';
				$res['msg'] = __('Data update failed');
			}
		}
		
		return response()->json($res);
    }

	//Get data for Client by id
    public function getClientById(Request $request){

		$id = $request->id;
		
		$data = DB::table('users')
					->join('countries', 'users.country_id', '=', 'countries.id')
					->select('users.*', 'countries.country_name')
					->where('users.id', $id)->first();
					
		$data->bactive = base64_decode($data->bactive);

		return response()->json($data);
	}
	
	//Delete data for Client
	public function deleteClient(Request $request){
		$res = array();
		$gtext = gtext();
		$mtext = mtext();
		
		$id = $request->id;
			
		$StaffObj = User::where('id', $id)->first();
		$StaffArr = $StaffObj->toArray();

		$Obj = Project::where('client_id', $id)->first();

		if(!empty($Obj)){
			$aRow = $Obj->toArray();
			$project_id = $aRow['id'];
			
			Attachment::where('project_id', $project_id)->delete();
			Comment::where('project_id', $project_id)->delete();
			Task_staff_map::where('project_id', $project_id)->delete();
			Task::where('project_id', $project_id)->delete();
			Task_group::where('project_id', $project_id)->delete();
			Project_staff_map::where('project_id', $project_id)->delete();
			Payment::where('project_id', $project_id)->delete();
			Project::where('id', $project_id)->delete();
		}
		
		ChatMessage::where('user_id', $id)->delete();
		ChatMessage::where('me_id', $id)->delete();
		DB::table('chat_login_status')->where('user_id', '=', $id)->delete();
	
		$response = User::where('id', $id)->delete();	
		if($response){
			$res['msgType'] = 'success';
			$res['msg'] = __('Data Removed Successfully');
		
			if($gtext['isnotification'] == 1){
				
				require 'vendor/autoload.php';
				$mail = new PHPMailer(true);

				//Send mail
				$mail->setFrom($gtext['fromMailAddress'], $gtext['company_name']);
				$mail->addAddress($StaffArr['email'], $StaffArr['name']);
				$mail->isHTML(true);
				$mail->CharSet = "utf-8";
				$mail->Subject = $mtext['Subject - Your account has been deleted'].' - '.$StaffArr['name'];
				$mail->Body = "<table style='background-color:#f0f0f0;color:#444;padding:40px 0px;line-height:24px;font-size:16px;' border='0' cellpadding='0' cellspacing='0' width='100%'>	
									<tr>
										<td>
											<table style='background-color:#fff;max-width:600px;margin:0 auto;padding:30px;' border='0' cellpadding='0' cellspacing='0' width='100%'>
												<tr><td style='font-size:30px;border-bottom:1px solid #ddd;padding-bottom:15px;font-weight:bold;text-align:center;'>".$gtext['company_name']."</td></tr>
												<tr><td style='font-size:20px;font-weight:bold;padding:30px 0px 5px 0px;'>Hi ".$StaffArr['name']."</td></tr>
												<tr><td style='padding-top:5px;padding-bottom:50px;'>".$mtext['Body - Your account has been deleted']."</td></tr>
												<tr><td style='padding-top:10px;border-top:1px solid #ddd;'>Thank you!</td></tr>
												<tr><td style='padding-top:5px;'><strong>".$gtext['company_name']."</strong></td></tr>
											</table>
										</td>
									</tr>
								</table>";
				$mail->send();
			}
		
		}else{
			$res['msgType'] = 'error';
			$res['msg'] = __('Data remove failed');
		}
		
		return response()->json($res);
	}	
}
