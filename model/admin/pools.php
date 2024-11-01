<?php
class VIC_Pools extends VIC_Model
{
    private static $teams;
    private static $fighters;
    public function __construct() 
    {
        $this->payment = new VIC_Payment();
        self::$teams = new VIC_Teams();
        self::$fighters = new VIC_Fighters();
    }
    
    //////////////////////////////////////////view//////////////////////////////////////////
    public function poolStatus()
    {
        return array('NEW', 'COMPLETE');
    }
    
    
    public function getPoolHours()
    {
        $data = array();
        for($i = 0; $i < 24; $i++)
        {
            if($i < 10)
            {
                $data[] = '0'.$i;
            }
            else 
            {
                $data[] = $i;
            }
        }
        return $data;
    }
    
    public function getPoolMinutes()
    {
        $data = array();
        for($i = 0; $i <= 55; $i++)
        {
            if($i < 10)
            {
                $data[] = '0'.$i;
            }
            else 
            {
                $data[] = $i;
            }
            $i+=4;
        }
        return $data;
    }
    
    public function isPoolExist($iPoolId = null)
    {
        if((int)$iPoolId > 0)
        {
            $data = $this->getPools((int)$iPoolId, null, false, true);
            if($data != null)
            {
                return true;
            }
        }
        return false;
    }
    
    public function isCompleteUserWin($leagueID)
    {
        global $wpdb;
        $table_name = $wpdb->prefix.'fundhistory';
        $sCond = "WHERE leagueID = $leagueID AND operation = 'ADD' AND type= 'WIN'";
        $sql = "SELECT COUNT(*) "
             . "FROM $table_name "
             . $sCond;
        $data = $wpdb->get_var($sql);
        if($data > 0)
        {
            return true;
        }
        return false;
    }
    
    public function isPoolResultsUpdated($iPoolId)
    {
        $aFights = $this->getFights($iPoolId, null, true);
        if(!empty($aFights))
        {
            foreach($aFights as $aFight)
            {
                if($aFight['winnerID'] != 0 && $aFight['winnerID'] != $aFight['fighterID1'] && $aFight['winnerID'] != $aFight['fighterID2'])
                {
                    return false;
                }
            }
        }
        return true;
    }

    public function getPools($iPoolId = null, $orgID = null, $isNew = false, $all = false)
    {
        $params = array('all' => $all);
        if((int)$iPoolId > 0)
        {
            $params['poolID'] = $iPoolId;
        }
        if((int)$orgID > 0)
        {
            $params['orgID'] = $orgID;
        }
        if($isNew)
        {
            $params['isNew'] = true;
        }
        $data = $this->sendRequest("pools", $params);
        if((int)$iPoolId > 0)
        {
            $data = $this->parsePoolsData($data);
            $data = $data[0];
        }
        return $data;
    }
    public function getMixingPools($listPools = null, $orgID = null, $isNew = false, $all = false)
    {
        $params = array('all' => $all);
        if(count($listPools) > 0)
        {
            $listPools = array_keys($listPools);
            $params["poolID"] = $listPools;
        }
        if((int)$orgID > 0)
        {
            $params['orgID'] = $orgID;
        }
        if($isNew)
        {
            $params['isNew'] = true;
        }
        $data = $this->sendRequest("mixingPools", $params);
        if(count($listPools) > 0)
        {
            $data = $this->parsePoolsData($data);
            //$data = $data[0];
        }
        return $data;
    }
    
    public function getNewPools()
    {
        return $this->sendRequest("getNewPools");
    }
    
    public function getTotalCurrentPools($orgID = null, $all = false)
    {
        $params = array();
        if((int)$orgID > 0)
        {
            $params['orgID'] = $orgID;
        }
        if($all)
        {
            $params['all'] = true;
        }
        $data = $this->sendRequest("totalCurrentPools", $params);
        return $data;
    }
    
    public function getPoolImageName($iPoolId)
    {
        $data = $this->getPools($iPoolId);
        if($data != null)
        {
            return $data['image'];
        }
        return null;
    }
    
    public function getPoolsByFilter($aConds, $sSort = 'poolID DESC', $iPage = '', $iLimit = '')
    {
        $params = array('aConds' => $aConds, 'sSort' => $sSort, 'iPage' => $iPage, 'iLimit' => $iLimit);
        $data = $this->sendRequest("poolsByFilter", $params);
        return array($data['iCnt'], $data['aRows']);
    }

    public function getFights($poolID, $fightID = null)
    {
        $params = array();
        if($poolID != null)
        {
            $params['poolID'] = $poolID;
        }
        if($fightID != null)
        {
            $params['fightID'] = $fightID;
        }
        $data = $this->sendRequest("fights", $params);
        $data = $this->parseFightsData($data);
        return $data;
    }
    public function getMotoCross($poolID, $fightID = null)
    {
        $params = array();
        if($poolID != null)
        {
            $params['poolID'] = $poolID;
        }
        if($fightID != null)
        {
            $params['fightID'] = $fightID;
        }
        $data = $this->sendRequest("motocross", $params);
        return $data;
    }
    
    public function getLeagues($poolID)
    {
        $params = array();
        if((int)$poolID > 0)
        {
            $params['poolID'] = $poolID;
        }
        return $this->sendRequest("leagues", $params);
    }
    
    
    
    public function parsePoolsData($data = null)
    {

        if($data != null)
        {
            foreach($data as $k => $v)
            {
                $data[$k]['full_image_path'] = isset($v['full_image_path']) ? VICTORIOUS_IMAGE_URL.$this->replaceSuffix($v['image']) : null;
                $data[$k]['result'] = null;

                //parse time
                $data[$k]['startHour'] = isset($v['startDate']) ? date('H', strtotime($v['startDate'])) : null; 
                $data[$k]['startMinute'] = isset($v['startDate']) ? date('i', strtotime($v['startDate'])) : null;
                $data[$k]['startDateOnly'] = isset($v['startDate']) ? date('Y-m-d', strtotime($v['startDate'])) : null; 
                
                $data[$k]['cutHour'] = isset($v['cutDate']) ? date('H', strtotime($v['cutDate'])) : null; 
                $data[$k]['cutMinute'] = isset($v['cutDate']) ? date('i', strtotime($v['cutDate'])) : null; 
                $data[$k]['cutDateOnly'] = isset($v['cutDate']) ? date('Y-m-d', strtotime($v['cutDate'])) : null; 
            }
        }
        return $data;
    }
    
    public function parseFightsData($data = null)
    {
        if($data != null)
        {
            $count = 0;
            foreach($data as $k => $v)
            {
                $count++;
                $data[$k]['count'] = $count;
                
                //parse time
                $data[$k]['startHour'] = isset($v['startDate']) ? date('H', strtotime($v['startDate'])) : null; 
                $data[$k]['startMinute'] = isset($v['startDate']) ? date('i', strtotime($v['startDate'])) : null;
                $data[$k]['startDateOnly'] = isset($v['startDate']) ? date('Y-m-d', strtotime($v['startDate'])) : null; 
            }
        }
        return $data;
    }
    
    public function parseFightsDataDetail($aFights = null, $poolType = '')
    {
        if($aFights != null)
        {
            $teamFighterIds = array();
            foreach($aFights as $k => $aFight)
            {
                $teamFighterIds[] = $aFight['fighterID1'];
                $teamFighterIds[] = $aFight['fighterID2'];
            }

            $aTeams = $aFighters = null;
            if(strtolower($poolType) == 'mma' || strtolower($poolType) == 'boxing')
            {
                //fighters
                self::$fighters->selectField(array('fighterID', 'name', 'nickName'));
                $aFighters = self::$fighters->getFighters($teamFighterIds);
            }
            else 
            {
                //teams
                self::$teams->selectField(array('teamID', 'name', 'nickName'));
                $aTeams = self::$teams->getTeams($teamFighterIds);
            }
            
            foreach($aFights as $k => $aFight)
            {
                if($aTeams != null)
                {
                    foreach($aTeams as $aTeam)
                    {
                        if($aTeam['teamID'] == $aFight['fighterID1'])
                        {
                            $aFights[$k]['nickName1'] = $aTeam['nickName'] != null ? $aTeam['nickName'] : $aTeam['name'];
                            $aFights[$k]['name1'] = $aTeam['name'];
                        }
                        if($aTeam['teamID'] == $aFight['fighterID2'])
                        {
                            $aFights[$k]['nickName2'] = $aTeam['nickName'] != null ? $aTeam['nickName'] : $aTeam['name'];
                            $aFights[$k]['name2'] = $aTeam['name'];
                        }
                    }
                }
                else if($aFighters != null)
                {
                    foreach($aFighters as $aFighter)
                    {
                        if($aFighter['fighterID'] == $aFight['fighterID1'])
                        {
                            $aFights[$k]['nickName1'] = $aFighter['nickName'] != null ? $aFighter['nickName'] : $aFighter['name'];
                            $aFights[$k]['name1'] = $aFighter['name'];
                        }
                        if($aFighter['fighterID'] == $aFight['fighterID2'])
                        {
                            $aFights[$k]['nickName2'] = $aFighter['nickName'] != null ? $aFighter['nickName'] : $aFighter['name'];
                            $aFights[$k]['name2'] = $aFighter['name'];
                        }
                    }
                }
            }
        }
        return $aFights;
    }
    
    public function calculatePrizes($type, $structure, $size, $entryFee, $payouts = null, $winnerPercent = 0, $firstPercent = 0, $secondPercent = 0, $thirdPercent = 0)
    {
        //default percent
        $winnerPercent = $winnerPercent == 0 ? get_option('victorious_winner_percent') : $winnerPercent;
        $firstPercent = $firstPercent == 0 ? get_option('victorious_first_place_percent') : $firstPercent;
        $secondPercent = $secondPercent == 0 ? get_option('victorious_second_place_percent') : $secondPercent;
        $thirdPercent = $thirdPercent == 0 ? get_option('victorious_third_place_percent') : $thirdPercent;
        
        $result = array();
        if($type == 'head2head')
        {
            $size = 2;
            $structure = "winnertakeall";
        }
		//if((int)$entryFee > 0)
        //{
            $prize = $size * $entryFee * $winnerPercent / 100;
            switch($structure)
            {
                case "winnertakeall":
                    $result["1st"] = round($prize, 2);
                    break;
                case "top3":
                    $result["1st"] = $this->addInsufficientZeroToMoneyFormat(round($prize * $firstPercent / 100, 2));//1st
                    $result["2nd"] = $this->addInsufficientZeroToMoneyFormat(round($prize * $secondPercent / 100, 2));//2nd
                    $result["3rd"] = $this->addInsufficientZeroToMoneyFormat(round($prize * $thirdPercent / 100, 2));//3th
                    break;
                case "multi_payout":
                    
                    if($payouts != null)
                    {
                        foreach($payouts as $payout)
                        {
                            $pos = '';
                            $range = ((int)$payout['to'] - (int)$payout['from']) + 1;
                            $from = $this->parsePosition($payout['from']);
                            $to = $this->parsePosition($payout['to']);
                            if($from == $to)
                            {
                                $pos = $from;
                            }
                            else 
                            {
                                $pos = $from." - ".$to;
                            }
                            $pos_prize = $this->addInsufficientZeroToMoneyFormat(round($prize * $payout['percent'] / 100, 2));
                            if($range > 0)
                            {
                                $pos_prize = round($pos_prize / $range, 2);
                            }
                            $result[$pos] = $pos_prize;
                        }
                    }
                    break;
                /*default :
                    break;*/
            }
        //}
        return $result;
    }
    
    private function parsePosition($num)
    {
        switch ($num)
        {
            case 1:
                $num = $num."st";
                break;
            case 2:
                $num = $num."nd";
                break;
            case 3:
                $num = $num."rd";
                break;
            default :
                $num = $num."th";
                break;
        }
        return $num;
    }
    
    private function addInsufficientZeroToMoneyFormat($str)
    {
        if ( substr($str, -2, 1) == '.' )
        {
            $str .= '0';
        }
        return $str;
    }
    
    public function loadPlayerPoints($poolID, $fightID, $playerID, $scoring_category_id)
    {
        $params = array('poolID' => $poolID,
                        'fightID' => $fightID,
                        'playerID' => $playerID,
                        'scoring_category_id' => $scoring_category_id);
        echo $this->sendRequest("loadPlayerPoints", $params, true, false);exit;
    }
    
    public function viewPlayerDraftResult($iPoolID)
    {
        return $this->sendRequest("viewPlayerDraftResult", array('poolID' => $iPoolID));
    }
    
    //////////////////////////////////////////add, update, delete pools//////////////////////////////////////////
    public function add($aVals)
    {
        $poolID = $this->sendRequest("addPools", $this->parsePoolsDataForModify($aVals));

        //upload new image
        $image = $this->uploadImage();
        $this->updatePoolsImage($poolID, $image);
        
        //add livepool
        if(isset($aVals['live_pool']) && $aVals['live_pool'] == 1)
        {
            $this->addLivePool($poolID);
        }
        else
        {
            $this->deleteLivePool($poolID);
        }
        
        //insert fight
        if((int)$poolID > 0)
        {
            $this->addFights($aVals, $poolID);
            return true;
        }
        return false;
    }
    
    public function addLivePool($poolID)
    {
        return $this->sendRequest("addLivePool", array('poolID' => $poolID));
    }
    
    public function deleteLivePool($poolID)
    {
        return $this->sendRequest("deleteLivePool", array('poolID' => $poolID));
    }
    
    public function addFights($aVals, $poolID)
    {
        foreach($aVals['fight'] as $index)
        {
            $data = $this->parseFightsDataForModify($aVals, $index, $poolID);
            $this->sendRequest("addFights", $data);
        }
    }
    
    public function update($aVals)
    {
        $result = $this->sendRequest("updatePools", $this->parsePoolsDataForModify($aVals, true));

        //add livepool
        if(isset($aVals['live_pool']) && $aVals['live_pool'] == 1)
        {
            $this->addLivePool($aVals['poolID']);
        }
        else
        {
            $this->deleteLivePool($aVals['poolID']);
        }
        
        //if new image uploaded, delete old image
        if (isset($_FILES['image']['name']) && ($_FILES['image']['name'] != ''))
        {
            //get current image name
            $sFileName = $this->getPoolImageName($aVals['poolID']);

            //delete old image
            $this->deleteImage($sFileName);
            
            //upload new image
            $image = $this->uploadImage();
            $this->updatePoolsImage($aVals['poolID'], $image);
        }

        //update fights
        if($result)
        {
            $this->updateFights($aVals, $aVals['poolID']);
            return true;
        }
        return false;
    }
    
    public function updatePoolsImage($poolID, $image)
    {
        $this->sendRequest("updatePools", array('poolID' => $poolID, 'image' => $image));
    }

    public function updatePoolStatus($poolID, $status = 'NEW')
    {
        $data = array('poolID' => $poolID,
                      'status' => $status);
        return $this->sendRequest("updatePools", $data);
    }
    
    public function updatePoolComplete($poolID)
    {
        if($this->sendRequest("updatePoolComplete", array('poolID' => $poolID), true, false))
        {
            $this->updatePoolStatus($poolID, 'COMPLETE');
            
            //update money for winners
            $this->updateUserMoneyWon();

            return true;
        }
        return false;
    }
    
    public function updateUserMoneyWon()
    {
        $aDatas = $this->sendRequest("userWonHistory");
        if($aDatas != null)
        {
            $success = true;
            $victorious = new VIC_Victorious();
            foreach($aDatas as $aData)
            {
                $funds = $this->payment->getFundhistoryList($aData['leagueID']);
				$params = $aData['leagueID']."_".$aData['userID']."_".$aData['entry_number']."_".$aData['operation']."_".$aData['type'];
                if(!isset($funds[$params]))
                {
                    $league = $victorious->getLeagueDetail($aData['leagueID']);
                    if($this->payment->updateUserBalance($aData['amount'], false, $aData['leagueID'], $aData['userID'], $league['balance_type_id']))
                    {
                        $aUser = $this->payment->getUserData($aData['userID']);
                        $params = array(
                            'userID' => $aData['userID'],
                            'amount' => $aData['amount'],
                            'leagueID' => $aData['leagueID'],
                            'new_balance' => $aUser['balance'],
                            'operation' => $aData['operation'],
                            'type' => $aData['type'],
                            'entry_number' => $aData['entry_number'],
                            'reason' => $aData['comment'],
                            'status' => 'completed'
                        );
                        $this->payment->addFundhistory($params);

                        //send email
                        $email = $aUser['email'];
                        $website = 'http://'.sanitize_url($_SERVER['SERVER_NAME']);
                        $siteTitle = get_option('blogname');
                        $place = '';
                        switch($aData['rank'])
                        {
                            case 1:
                                $place = '1st';
                                break;
                            case 2:
                                $place = '2nd';
                                break;
                            case 3:
                                $place = '3th';
                                break;
                        }
                        $headers  = 'MIME-Version: 1.0' . "\r\n";
                        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
                        $headers .= 'To: ' . $email . "\r\n";
                        $headers .= "From: ".get_option('blogname')." <".get_option('admin_email').">\r\n";
                        //$headers .= 'Bcc: ' . $myEmail . "\r\n";
                        $emailInfo = array('league_name' => $aData['league_name'],
                                           'username' => $aUser['user_name'],
                                           'money' => $aData['amount'],
                                           'place' => $place);

                        if($aData['type'] == "WIN")
                        {
                            include 'emailTemplates/wonLeague.php';
                        }
                        else if($aData['type'] == "REFUND")
                        {
                            include 'emailTemplates/leagueNotFilledNotice.php';
                        }
                        try 
                        {
                            wp_mail($email, $message_subject, $message_body, $headers);
                        } 
                        catch (Exception $ex) 
                        {

                        }
                        
                        //buddy press integration
                        if($league != null)
                        {
                            $league = $league[0];
                            $victorious->addWonContestActivity($league, $aData['userID']);
                        }
                    }
                    else
                    {
                        $success = false;
                    }
                }
            }
        }
    }
    
    public function reverseResult($iPoolID)
    {
        $result = $this->sendRequest("reverseResult", array('poolID' => $iPoolID), true, false);
        switch($result)
        {
            case 2:
                return 2;
                break;
            default :
                $this->updateReverseMoney($result);
                return 1;
        }
        return 0;
    }
    
    public function updateReverseMoney($leagueIDs)
    {
        global $wpdb;
        $table_fundhistory = $wpdb->prefix."fundhistory";
        $table_user_extended = $wpdb->prefix."user_extended";
        $leagueIDs = json_decode($leagueIDs);
        if($leagueIDs != null)
        {
            foreach($leagueIDs as $leagueID)
            {
                $league = $victorious->getLeagueDetail($leagueID);
                
                //get list fundhistory
                $sql = "SELECT fundshistoryID, amount,userID,type "
                        . "FROM $table_fundhistory "
                        . "WHERE leagueID = $leagueID AND operation = 'ADD'";
                $aFunds = $wpdb->get_results($sql);
                
                //deduct balance
                if($aFunds != null)
                {
                    foreach($aFunds as $aFund)
                    {
                        $this->payment->updateUserBalance($aFund->amount, true, 0, $aFund->userID, $league['balance_type_id']);
                        $wpdb->delete($table_fundhistory, array('fundshistoryID' => $aFund->fundshistoryID));
                        $this->sendReverseEmail($aFund->userID, $leagueID);
                    }
                }
            }
        }
    }
    
    private function sendReverseEmail($user_id, $leagueID)
    {
        $aUser = $this->payment->getUserData($user_id);
        $email = $aUser['email'];
        $website = 'http://'.sanitize_url($_SERVER['SERVER_NAME']);
        $siteTitle = get_option('blogname');
        
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'To: ' . $email . "\r\n";
        $headers .= "From: ".get_option('blogname')." <".get_option('admin_email').">\r\n";
        //$headers .= 'Bcc: ' . $myEmail . "\r\n";
        $emailInfo = array('league_name' => $leagueID);
        include 'emailTemplates/reverseLeague.php';
        try 
        {
            wp_mail($email, $message_subject, $message_body, $headers);
        } 
        catch (Exception $ex) 
        {

        }
    }

    public function updateFights($aVals, $poolID)
    {
        if(isset($aVals['fight']))
        {
            $newFightIds = array();
            //load current fight
            $curFight = $this->getFights($poolID);
            $curFightID = array();
            if($curFight != null)
            {
                foreach($curFight as $item)
                {
                    $curFightID[] = $item['fightID'];
                }
            }

            //parse fight to update, add new or delete
            foreach($aVals['fight'] as $index)
            {
                $fightID = $aVals['fightID'][$index];
                if((int)$fightID > 0 && in_array($fightID, $curFightID)) //update
                {
                    $data = $this->parseFightsDataForModify($aVals, $index, $poolID, true);
                    $this->sendRequest("updateFights", $data);

                    //clear updated fight
                    if(($key = array_search($fightID, $curFightID)) !== false)
                    {
                        unset($curFightID[$key]);
                        array_values($curFightID);
                    }
                }
                else //add
                {
                    $data = $this->parseFightsDataForModify($aVals, $index, $poolID);
                    $newFightIds[] = $this->sendRequest("addFights", $data);
                }
            }

            //update new fixture for league
            if($newFightIds != null)
            {
                $newFightIds = implode(',', $newFightIds);
                $aLeagues = $this->getLeagues($poolID);
                if($aLeagues != null)
                {
                    foreach($aLeagues as $aLeague)
                    {
                        $fixtures = $aLeague['fixtures'].','.$newFightIds;
                        $data = array('leagueID' => $aLeague['leagueID'], 'fixtures' => $fixtures); 
                        $data = $this->sendRequest("updateLeague", $data);
                    }
                }
            }

            //delete
            foreach($curFightID as $item)
            {
                $data = array('poolID' => $poolID, 'fightID' => $item);
                $this->sendRequest("deleteFights", $data);
            }
        }
    }
    
    public function updateFightResult($data)
    {
        if($this->sendRequest("updateFights", $data))
        {
            return true;
        }
        return false;
    }
    
    public function updatePlayerDraftResult($data)
    {
        if($this->sendRequest("addPlayerStats", $data))
        {
            return true;
        }
        return false;
    }

    private function parsePoolsDataForModify($aVals, $isUpdate = false)
    {
        $lineup = array();
        
        if(isset($aVals['lineup']) && $aVals['lineup'] != null && is_array($aVals['lineup']))
        {
            foreach($aVals['lineup'] as $k => $v)
            {
                $lineup[] = array('id' => $k, 
                                  'total' => $v['total'], 
                                  'enable' => isset($v['enable']) ? 1 : 0);
            }
            $lineup = json_encode($lineup);
        }
        else 
        {
            $lineup = $aVals['lineup'];
        }

        $data = array('poolName' => str_replace("\'", "'", $aVals['poolName']),
                      'startDate' => $aVals['startDate'].' '.$aVals['startHour'].':'.$aVals['startMinute'].':00',
                      'cutDate' => $aVals['cutDate'].' '.$aVals['cutHour'].':'.$aVals['cutMinute'].':00',
                      'organization' => $aVals['organization'],
                      'type' => isset($aVals['type']) ? $aVals['type'] : '',
                      'salary_remaining' => str_replace(',', '', $aVals['salary_remaining']),
                      'lineup' => $lineup,
                      'rounds' => isset($aVals['rounds']) ? $aVals['rounds'] : '',
                      'is_motocross'=>isset($aVals['is_motocross']) ? $aVals['is_motocross'] : '');
        if($isUpdate)
        {
            $data['poolID'] = $aVals['poolID'];
        }
        return $data;
    }
    
    private function parseFightsDataForModify($aVals, $index, $poolID, $isUpdate = false)
    {
        $data = array(
            'poolID' => $poolID,
            'fighterID1' => $aVals['fighterID1'][$index],
            'fighterID2' => $aVals['fighterID2'][$index],
            'name' => $aVals['fight_name'][$index],
            'startDate' => $aVals['fight_startDate'][$index].' '.$aVals['fight_startHour'][$index].':'.$aVals['fight_startMinute'][$index].':00',
            'champFight' => isset($aVals['champFight'][$index]) && $aVals['champFight'][$index] == 1 ? 'YES' : 'NO',
            'amateurFight' => isset($aVals['amateurFight'][$index]) && $aVals['amateurFight'][$index] == 1 ? 'YES' : 'NO',
            'mainFight' => isset($aVals['mainFight'][$index]) && $aVals['mainFight'][$index] == 1 ? 'YES' : 'NO',
            'prelimFight' => isset($aVals['prelimFight'][$index]) && $aVals['prelimFight'][$index] == 1 ? 'YES' : 'NO',
            'rounds' => $aVals['rounds'][$index],
            'fightOrder' => $index,
            'team1_win' => isset($aVals['team1_win'][$index]) ? $aVals['team1_win'][$index] : 0,
            'team2_win' => isset($aVals['team2_win'][$index]) ? $aVals['team2_win'][$index] : 0,
            'team_draw' => isset($aVals['team_draw'][$index]) ? $aVals['team_draw'][$index] : 0,
            'total_over_under' => isset($aVals['total_over_under'][$index]) ? $aVals['total_over_under'][$index] : 0,
            'total_over' => isset($aVals['total_over'][$index]) ? $aVals['total_over'][$index] : 0,
            'total_under' => isset($aVals['total_under'][$index]) ? $aVals['total_under'][$index] : 0,
            'team1_spread' => isset($aVals['team1_spread'][$index]) ? $aVals['team1_spread'][$index] : 0,
            'team2_spread' => isset($aVals['team2_spread'][$index]) ? $aVals['team2_spread'][$index] : 0,
            'team1_spread_points' => isset($aVals['team1_spread_points'][$index]) ? $aVals['team1_spread_points'][$index] : 0,
            'team2_spread_points' => isset($aVals['team2_spread_points'][$index]) ? $aVals['team2_spread_points'][$index] : 0,
        );
        if($isUpdate)
        {
            $data['fightID'] = $aVals['fightID'][$index];
        }
        return $data;
    }

    public function delete($poolId)
    {
        $sFileName = $this->getPoolImageName($poolId);
        $result = $this->sendRequest("deletePools", array('poolID' => $poolId));
        if($result)
        {
            $this->deleteImage($sFileName);
            return true;
        }
        return false;
    }
    
    public function cancelContest($params)
    {
        $result = $this->sendRequest("cancelContest", $params, true, false);
        if($result == 1)
        {
            $leagueID = $params['leagueID'];
            $league = $result['league'];
            global $wpdb;
            $table_fundhistory = $wpdb->prefix."fundhistory";
            $table_user_extended = $wpdb->prefix."user_extended";
            
            //deduct balance
            $sql = "SELECT fundshistoryID, amount,userID,type "
                    . "FROM $table_fundhistory "
                    . "WHERE leagueID = $leagueID AND operation = 'ADD' AND type = 'WIN'";
            $aFunds = $wpdb->get_results($sql);

            if($aFunds != null)
            {
                foreach($aFunds as $aFund)
                {
                    $this->payment->updateUserBalance($aFund->amount, true, 0, $aFund->userID, $league['balance_type_id']);
                    $wpdb->delete($table_fundhistory, array('fundshistoryID' => $aFund->fundshistoryID));
                }
            }
            
            //add balance
            $sql = "SELECT fundshistoryID, amount,userID,type "
                    . "FROM $table_fundhistory "
                    . "WHERE leagueID = $leagueID AND operation = 'DEDUCT' AND type = 'MAKE_BET'";
            $aFunds = $wpdb->get_results($sql);
            
            if($aFunds != null)
            {
                foreach($aFunds as $aFund)
                {
                    $this->payment->updateUserBalance($aFund->amount, false, 0, $aFund->userID, $league['balance_type_id']);
                    $wpdb->delete($table_fundhistory, array('fundshistoryID' => $aFund->fundshistoryID));
                    $this->sendCancelEmail($aFund->userID, $leagueID);
                }
            }
        }
        return $result;
    }
    
    private function sendCancelEmail($user_id, $leagueID)
    {
        $aUser = $this->payment->getUserData($user_id);
        $email = $aUser['email'];
        $website = 'http://'.sanitize_url($_SERVER['SERVER_NAME']);
        $siteTitle = get_option('blogname');
        
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'To: ' . $email . "\r\n";
        $headers .= "From: ".get_option('blogname')." <".get_option('admin_email').">\r\n";
        //$headers .= 'Bcc: ' . $myEmail . "\r\n";
        $emailInfo = array('league_name' => $leagueID);
        include 'emailTemplates/cancelLeague.php';
        try 
        {
            wp_mail($email, $message_subject, $message_body, $headers);
        } 
        catch (Exception $ex) 
        {

        }
    }
}
?>