<?php
class VIC_GlobalController
{
    public function trigger_check_playoff(){
        require_once(VICTORIOUS__PLUGIN_DIR_MODEL."playoff.php");
		$mPlayoff = new VIC_Playoff();

        $result = $mPlayoff->playoffCheckDraftStart();

        if(!empty($result['contests'])){
            foreach($result['contests'] as $contest){
                $league = $contest['league'];
                $user_ids = $contest['user_ids'];
                $cancel_contest = $contest['cancel_contest'];
                if(empty($user_ids)){
                    continue;
                }
                foreach($user_ids as $user_id){
                    if($cancel_contest){
                        $mPlayoff->sendCancelContestEmail($user_id, $league);
                    }
                    else{
                        $mPlayoff->sendStartDraftEmail($user_id, $league);
                    }
                }

                if(!$cancel_contest) {
                    $mPlayoff->updatePlayoffTurn($league['leagueID'], '');
                }
            }
        }

        exit;
    }

    public static function trigger_playoff()
    {
		require_once(VICTORIOUS__PLUGIN_DIR_MODEL."playoff.php");
		$mPlayoff = new VIC_Playoff();

        $result = $mPlayoff->playoffTrigger();

        if(!empty($result['current_turn_user_id'])){
            foreach($result['current_turn_user_id'] as $league_id => $user_id){
                $mPlayoff->updatePlayoffTurn($league_id, $user_id);
            }
        }

		exit;
    }
}

?>