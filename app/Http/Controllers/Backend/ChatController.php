<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\ChatMessage;
use App\User;
use Carbon\Carbon;
use Auth;

class ChatController extends Controller
{
    //Chat page load
    public function getChatPageLoad(){
        return view('backend.chat');
    }
	
	//Get data for User
    public function getUserList(Request $request){
		$gtext = gtext();
		$timezone = $gtext['timezone'];

		$me_id = $request->input('me_id');
		$search = $request->input('search');

		$data = DB::table('users')
			->leftJoin('chat_login_status', 'users.id', '=', 'chat_login_status.user_id')
			->select('users.id', 'users.name', 'users.photo', 'chat_login_status.is_active', 'chat_login_status.login_datetime')
			->where('users.active_id', 1)
			->whereNotIn('users.id', [$me_id])
			->where(function($query) use ($search){
				$query->where('name', 'LIKE', '%'.$search.'%')
					->orWhere('email', 'LIKE', '%'.$search.'%');
			})
			->orderBy("chat_login_status.login_datetime", "desc")
			->get();

		for($i=0; $i<count($data); $i++){
			
			if($data[$i]->login_datetime != ''){
				date_default_timezone_set($timezone);
				$currDateTime = date("d M Y");
				
				date_default_timezone_set($timezone);
				$logdate = strtotime($data[$i]->login_datetime);
				$logDateTime = date("d M Y", $logdate);
				
				if($currDateTime == $logDateTime){
					date_default_timezone_set($timezone);
					$tdate = strtotime($data[$i]->login_datetime);
					$data[$i]->login_datetime = __('Today').' '.date("h:i A", $tdate);
				}else{
					date_default_timezone_set($timezone);
					$tdate = strtotime($data[$i]->login_datetime);
					$login_datetime = date("d M Y h:i A", $tdate);
					$data[$i]->login_datetime = $login_datetime;
				}

				$data[$i]->notDatetime = '';
			}else{
				$data[$i]->login_datetime = '';
				$data[$i]->notDatetime = 'paddingTop10';
			}

			if($data[$i]->is_active != ''){
				$data[$i]->is_active = $data[$i]->is_active;
			}else{
				$data[$i]->is_active = 0;
			}
			
			$data[$i]->is_count = self::getCount($data[$i]->id);
		}

		return response()->json($data);
	}
	
	public function getCount($id) {
		$user = auth()->user();
		$user_id = $user->id;

		$data = ChatMessage::where('me_id', $id)
		->where('user_id', $user_id)
		->where('is_seen', 0)
		->get()->count();
		return $data;
    }
	
	//Get data for User by id
    public function getUserById(Request $request){

		$id = $request->id;
		$data = DB::table('users')->where('id', $id)->first();
		
		return response()->json($data);
	}
	
	//Save data for Sent Message
    public function SaveMessage(Request $request){
		$res = array();
		$gtext = gtext();
		$timezone = $gtext['timezone'];
		date_default_timezone_set($timezone);
		$chat_datetime = date("Y-m-d H:i:s");

		$id = $request->input('message_id');
		$user_id = $request->input('user_id');
		$me_id = $request->input('me_id');
		$chat_mes_text = $request->input('chat_mes_text');
		
		if($id ==''){
			$data = array(
				'user_id' => $user_id,
				'me_id' => $me_id,
				'chat_mes_text' => $chat_mes_text,
				'chat_datetime' => $chat_datetime,
				'is_me_id' => $me_id,
				'is_seen' => 0
			);
			$response = ChatMessage::create($data);
			if($response){
				$res['msgType'] = 'success';
				$res['msg'] = __('New Data Added Successfully');
			}else{
				$res['msgType'] = 'error';
				$res['msg'] = __('Data insert failed');
			}
		}else{
			$data = array(
				'chat_mes_text' => $chat_mes_text
			);
			$response = ChatMessage::where('id', $id)->update($data);
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
	
	//Save files
    public function SaveFile(Request $request){
		$res = array();
		$gtext = gtext();
		$timezone = $gtext['timezone'];
		date_default_timezone_set($timezone);
		$chat_datetime = date("Y-m-d H:i:s");

		$user_id = $request->input('user_id');
		$me_id = $request->input('me_id');
		$files = $request->input('files');
		
		$index = 0;		
		if($files != ''){
			$fList = explode("|",$files);
			foreach ($fList as $key => $file) {
				
				$Filetype = strtolower(pathinfo($file, PATHINFO_EXTENSION));
				
				if (($Filetype == 'jpg') || ($Filetype == 'jpeg') || ($Filetype == 'png') || ($Filetype == 'gif') || ($Filetype == 'PNG') || ($Filetype == 'JPG') || ($Filetype == 'JPEG') || ($Filetype == 'ico')) {
					$data = array(
						'user_id' => $user_id,
						'me_id' => $me_id,
						'chat_mes_img' => $file,
						'chat_datetime' => $chat_datetime,
						'is_me_id' => $me_id,
						'is_seen' => 0
					);
				}else{
					$data = array(
						'user_id' => $user_id,
						'me_id' => $me_id,
						'chat_mes_file' => $file,
						'chat_datetime' => $chat_datetime,
						'is_me_id' => $me_id,
						'is_seen' => 0
					);
				}
				
				ChatMessage::create($data);
				
				$index++;
			}
		}

		if($index != 0){
			$res['msgType'] = 'success';
			$res['msg'] = __('New Data Added Successfully');
		}else{
			$res['msgType'] = 'error';
			$res['msg'] = __('Data insert failed');
		}
		
		return response()->json($res);
    }
	
	//Get data for Message
    public function getMessageList(Request $request){
		$gtext = gtext();
		$timezone = $gtext['timezone'];

		$me_id = $request->input('me_id');
		$user_id = $request->input('user_id');
		$search = $request->input('search');

		$limit = self::limit($request);

		$strWhLike = "";
		if ($search != "") {
			$strWhLike = " AND (a.chat_mes_text LIKE '%" . $search . "%'
				OR a.chat_mes_file LIKE '%" . $search . "%'
				OR a.chat_mes_img LIKE '%" . $search . "%') ";
		}
	
		if($search != ''){
			$query = "SELECT * FROM (SELECT 
			a.id, 
			a.user_id, 
			b.name, 
			b.photo, 
			a.me_id, 
			a.chat_mes_text, 
			a.chat_mes_file, 
			a.chat_mes_img, 
			a.is_delete, 
			a.is_seen, 
			a.is_me_id, 
			a.is_group, 
			a.chat_datetime
			FROM chat_messages a
			INNER JOIN users b ON a.is_me_id = b.id
			WHERE ((a.user_id = '".$me_id."' AND a.me_id = '".$user_id."')
			OR (a.user_id = '".$user_id."' AND a.me_id = '".$me_id."'))
			$strWhLike
			ORDER BY a.id DESC) t ORDER BY id ASC;";
			
		}else{
			$query = "SELECT * FROM (SELECT 
			a.id, 
			a.user_id, 
			b.name, 
			b.photo, 
			a.me_id, 
			a.chat_mes_text, 
			a.chat_mes_file, 
			a.chat_mes_img, 
			a.is_delete, 
			a.is_seen, 
			a.is_me_id, 
			a.is_group, 
			a.chat_datetime
			FROM chat_messages a
			INNER JOIN users b ON a.is_me_id = b.id
			WHERE (a.user_id = '".$me_id."' AND a.me_id = '".$user_id."')
			OR (a.user_id = '".$user_id."' AND a.me_id = '".$me_id."')
			ORDER BY a.id DESC $limit) t ORDER BY id ASC;";
		}

		$data = DB::select(DB::raw($query));

		for($i=0; $i<count($data); $i++){
			
			date_default_timezone_set($timezone);
			$currDateTime = date("d M Y");
			
			date_default_timezone_set($timezone);
			$chatdate = strtotime($data[$i]->chat_datetime);
			$oldDateTime = date("d M Y", $chatdate);
			
			if($currDateTime == $oldDateTime){
				date_default_timezone_set($timezone);
				$tdate = strtotime($data[$i]->chat_datetime);
				$chat_datetime = __('Today').' '.date("h:i A", $tdate);
			}else{
				date_default_timezone_set($timezone);
				$tdate = strtotime($data[$i]->chat_datetime);
				$chat_datetime = date("d M Y h:i A", $tdate);
			}
			
			$data[$i]->chat_datetime = $chat_datetime;
		}
		
		return response()->json($data);
	}
	
    public function limit($request) {
        $limit = '';
        if ($request->has('start') && $request->input('length') != -1) {
            $limit = "LIMIT " . intval($request->input('start')) . ", " . intval($request->input('length'));
        }
        return $limit;
    }
	
	//Delete data for Message
	public function deleteMessageById(Request $request){
		
		$res = array();

		$id = $request->id;
		
		$response = ChatMessage::where('id', $id)->delete();	
		if($response){
			$res['msgType'] = 'success';
			$res['msg'] = __('Data Removed Successfully');
		}else{
			$res['msgType'] = 'error';
			$res['msg'] = __('Data remove failed');
		}
		
		return response()->json($res);
	}
	
	//Get data for Message by id
    public function editMessageById(Request $request){

		$id = $request->id;
		$data = ChatMessage::where('id', $id)->first();
		
		return response()->json($data);
	}
	
	//Message Seen Save
    public function MessageSeenSave(Request $request){
		$res = array();
		$user_id = $request->user_id;
		$me_id = $request->me_id;

		$data = array('is_seen' => 1);
		$response = ChatMessage::where('user_id', $me_id)
		->where('me_id', $user_id)
		->where('is_seen', 0)
		->update($data);

		if($response == 1){
			$res['msgType'] = 'success';
			$res['msg'] = __('Data Updated Successfully');
		}else{
			$res['msgType'] = '';
			$res['msg'] = '';
		}

		return response()->json($res);
	}
}
