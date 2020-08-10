<?php

namespace App\Http\Controllers;

use App\Conversation;
use App\Messages;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class messageController extends Controller
{
	protected $authUserId;

    public function index(User $user)
    {
    	$message = [];
		$conv; 

		if (! $this->isExistsAmongTwoUsers(Auth::id(),$user->id)) {
			$this->newConversation($user->id);
		}

    	if ( $conv =  $this->isExistsAmongTwoUsers(Auth::id(),$user->id)) {
    		$message =  Messages::where('conversation_id',$conv)->get();
    	}

		return view('message',[
			'users' => User::all(),
			'conv' => $conv,
			'messages' => $message,
			'receiverid' => $user
		]);

    	// return back();
    }

        /*
     * check this given two users is already make a conversation
     *
     * @param   int $user1
     * @param   int $user2
     * @return  int|bool
     * */
    public function isExistsAmongTwoUsers($user1, $user2)
    {
        $conversation = Conversation::where(
            function ($query) use ($user1, $user2) {
                $query->where(
                    function ($q) use ($user1, $user2) {
                        $q->where('user_one', $user1)
                            ->where('user_two', $user2);
                    }
                )
                    ->orWhere(
                        function ($q) use ($user1, $user2) {
                            $q->where('user_one', $user2)
                                ->where('user_two', $user1);
                        }
                    );
            }
        );

        if ($conversation->exists()) {
            return $conversation->first()->id;
        }

        return false;
    }


    public function sendMessage(Request $request,$conversatonId)
    {
    	if ( $conversatonId && $request->message ) {
    		if ($this->existsById($conversatonId)){
    		    $this->makeMessage($conversatonId,$request->message);  
    		}
    	}
    	return back();
    }

       /*
     * check this given user is involved with this given $conversation
     *
     * @param   int $conversationId
     * @param   int $userId
     * @return  bool
     * */
    public function isUserExists($conversationId, $userId)
    {
        $exists = Conversation::where('id', $conversationId)
            ->where(
                function ($query) use ($userId) {
                    $query->where('user_one', $userId)->orWhere('user_two', $userId);
                }
            )
            ->exists();

        return $exists;
    }

        /*
     * check this given user is exists
     *
     * @param   int $id
     * @return  bool
     * */
    public function existsById($id)
    {
        $conversation = Conversation::find($id);
        if ($conversation) {
            return true;
        }

        return false;
    }

    protected function makeMessage($conversations, $message)
    {
        $message = Messages::create([
            'message' => $message,
            'conversation_id' => $conversations,
            'user_id' => Auth::id(),
            'is_seen' => 0,
        ]);
        return $message;
    }





       /**
     * make two users as serialize with ascending order.
     *
     * @param int $user1
     * @param int $user2
     *
     * @return array
     */
    protected function getSerializeUser($user1, $user2)
    {
        $user = [];
        $user['one'] = ($user1 < $user2) ? $user1 : $user2;
        $user['two'] = ($user1 < $user2) ? $user2 : $user1;

        return $user;
    }

        /**
     * make new conversation the given receiverId with currently loggedin user.
     *
     * @param int $receiverId
     *
     * @return int
     */
    protected function newConversation($receiverId)
    {
        $conversationId = $this->isConversationExists($receiverId);
        $user = $this->getSerializeUser( Auth::id() , $receiverId);

        if ($conversationId === false) {
            $conversation = Conversation::create([
                'user_one' => $user['one'],
                'user_two' => $user['two'],
                // 'status' => 1,
            ]);

            if ($conversation) {
                return $conversation->id;
            }
        }

        return $conversationId;
    }

        /**
     * make sure is this conversation exist for this user with currently loggedin user.
     *
     * @param int $userId
     *
     * @return bool|int
     */
    public function isConversationExists($userId)
    {
        if (empty($userId)) {
            return false;
        }

        $user = $this->getSerializeUser(Auth::id(), $userId);

        return $this->isExistsAmongTwoUsers($user['one'], $user['two']);
    }

}
