<?php
function js_lang()
{
    echo '
    <script type="text/javascript">
        var wpfs={
            "countdown" : "'.__(": : : : day week month year decade century millennium", "victorious").'",
            "countdown1" : "'.__(": : : : days weeks months years decades centurys millenniums", "victorious").'",
            "pleasewait" : "'.__("Loading...Please wait!", "victorious").'",
            "Name" : "'.__("Name:", "victorious").'",
            "Entry Fee" : "'.__("Entry Fee", "victorious").'",
            "Prizes" : "'.__("Prizes", "victorious").'",
            "Prize Structure" : "'.__("Prize Structure:", "victorious").'",
            "Creator" : "'.__("Creator:", "victorious").'",
            "Sport" : "'.__("Sport:", "victorious").'",
            "Game Type" : "'.__("Game Type:", "victorious").'",
            "Start" : "'.__("Start:", "victorious").'",
            "End1" : "'.__("End:", "victorious").'",
            "End2" : "'.__("Prizes paid next day", "victorious").'",
            "no_entry_yet" : "'.__("This game doesn\'t have any entries yet", "victorious").'",
            "Normal" : "'.__("Normal:", "victorious").'",
            "Playerdraft:" : "'.__("Playerdraft:", "victorious").'",
            "info" : "'.__("Info", "victorious").'",
            "fixture" : "'.__("Fixture", "victorious").'",
            "scoring" : "'.__("Scoring", "victorious").'",
            "set_pick" : "'.__("Please set your picks.", "victorious").'",
            "cut_date_expired" : "'.__("Sorry, you can\'t save your picks for this pool because the cut date is expired.", "victorious").'",
            "see_pick_after_league_start" : "'.__("You can see another user\'s picks after league start only.", "victorious").'",
            "fight_no_result" : "'.__("No results for this fight", "victorious").'",
            "cant_display_pick" : "'.__("Cannot display picks.", "victorious").'",
            "h_User" : "'.__("User", "victorious").'",
            "h_Rank" : "'.__("Rank", "victorious").'",
            "h_Points" : "'.__("Points", "victorious").'",
            "h_Winners" : "'.__("Winners", "victorious").'",
            "h_Methods" : "'.__("Methods", "victorious").'",
            "h_Rounds" : "'.__("Rounds", "victorious").'",
            "h_Minutes" : "'.__("Minutes", "victorious").'",
            "h_Bonuses" : "'.__("Bonuses", "victorious").'",
            "h_Winnings" : "'.__("Winnings", "victorious").'",
            "fight_no_result" : "'.__("No results for this fight", "victorious").'",
            "fullpositions1" : "'.__("Player cannot be added - all", "victorious").'",
            "fullpositions2" : "'.__(" positions are filled", "victorious").'",
            "players_out_team" : "'.__("Are you sure you want to clear all players from your team?", "victorious").'",
            "player_each_position" : "'.__("Please select a player for each position", "victorious").'",
            "golfskin_player_position" : "'.__("Please select at least one player for one round", "victorious").'",
            "golfskin_add_balance" : "'.__("Your balance is not enough, Please add more", "victorious").'",
            "team_exceed_salary_cap" : "'.__("Your team has exceeded this game\'s salary cap. Please change your team so it fits under the salary cap before entering", "victorious").'",
            "player_no_match" : "'.__("This player has not played any matches yet.", "victorious").'",
            "pick_a_team" : "'.__("Pick a team of players from the following games", "victorious").'",
            "pick_player_from_list" : "'.__("Pick players from", "victorious").'",
            "no_contest_entry" : "'.__("There are no entries in this contest yet.", "victorious").'",
            "input_picks" : "'.__("Please select all your teams before saving.", "victorious").'",
            "input_picks_only_one" : "'.__("Please select a team before saving.", "victorious").'",
            "number_decimal" : "'.__("Number can only be 2 decimal places at most", "victorious").'",
            "valid_amount" : "'.__("Please enter a valid withdrawal amount", "victorious").'",
            "withdraw_amount" : "'.__("Please enter an amount to withdrawal", "victorious").'",
            "invalid_amount" : "'.__("Entered amount is greater than your balance. Please re-enter an amount less than $", "victorious").'",
            "invalid_email" : "'.__("Please enter a valid email address", "victorious").'",
            "a_sure" : "'.__("Are you sure?", "victorious").'",
            "a_sb_organization" : "'.__("--Please select organization first--", "victorious").'",
            "a_name" : "'.__("Name", "victorious").'",
            "a_points" : "'.__("Points", "victorious").'",
            "a_prizes" : "'.__("Prizes", "victorious").'",
            "a_awarded" : "'.__("Awarded", "victorious").'",
            "a_fee" : "'.__("Entry Fee", "victorious").'",
            "a_size" : "'.__("Size", "victorious").'",
            "a_entries" : "'.__("Entries", "victorious").'",
            "a_total" : "'.__("Total Cash", "victorious").'",
            "edit" : "'.__("Edit", "victorious").'",
            "enter" : "'.__("Enter", "victorious").'",
            "free" : "'.__("Free", "victorious").'",
            "position" : "'.__("Position", "victorious").'",
            "salary_cap" : "'.__("Salary Cap", "victorious").'",
            "contest" : "'.__("Contest", "victorious").'",
            "scoring_categories" : "'.__("Scoring Categories", "victorious").'",
            "view" : "'.__("View", "victorious").'",
            "close" : "'.__("Close", "victorious").'",
            "send" : "'.__("Send", "victorious").'",
            "cancel" : "'.__("Cancel", "victorious").'",
            "from" : "'.__("From", "victorious").'",
            "to" : "'.__("To", "victorious").'",
            "add" : "'.__("Add", "victorious").'",
            "delete" : "'.__("Delete", "victorious").'",
            "summary" : "'.__("Summary", "victorious").'",
            "game_log" : "'.__("Game Log", "victorious").'",
            "player_news" : "'.__("Player News", "victorious").'",
            "salary" : "'.__("Salary", "victorious").'",
            "played" : "'.__("Played", "victorious").'",
            "season_statistics" : "'.__("Season Statistics", "victorious").'",
            "latest_player_news" : "'.__("Latest Player News", "victorious").'",
            "played" : "'.__("Played", "victorious").'",
            "remove_player" : "'.__("Remove Player", "victorious").'",
            "next_game" : "'.__("Next Game", "victorious").'",
            "updating" : "'.__("Updating", "victorious").'",
            "no_news" : "'.__("No news", "victorious").'",
            "here" : "'.__("here", "victorious").'",
            "opposing_pitcher" : "'.__("Opposing Pitcher", "victorious").'",
            "create_contest" : "'.__("Create Contest", "victorious").'",
            "working" : "'.__("Working", "victorious").'",
            "registriction_player" : "'.__("You are only able to pick ", "victorious").'",
            "registriction_forteam" : "'.__(" player(s) for a team. ", "victorious").'",
            "restriction_change_players" : "'.__(" The number of players a user can change per week ", "victorious").'",
            "injury_remove_title" : "'.__("Remove injury player", "victorious").'",
            "user_list" : "'.__("User List", "victorious").'",
            "confirm_change_player_with_other_user" : "'.__("Are you sure you want to send request?", "victorious").'",
            "sending" : "'.__("Sending...", "victorious").'",
            "joined" : "'.__("Joined", "victorious").'",
            "turn_by_turn_draft_a_player" : "'.__("Please draft a player", "victorious").'",
            "your_lineup" : "'.__("Your lineup", "victorious").'",
            "lineup" : "'.__("lineup", "victorious").'",
            "user_rank" : "'.__("Rank", "victorious").'",
            "invite_friends" : "'.__("Invite Friends", "victorious").'",
            "no_event_for_live_draft" : "'.__("No event for live draft", "victorious").'",
            "contest_cancelled" : "'.__("Cancelled", "victorious").'",
            "test_connection" : "'.__("Test connection to the Victorious servers", "victorious").'",
            "test_connection_testing" : "'.__("Testing", "victorious").'",
            "action" : "'.__("Action", "victorious").'",
            "VICTORIOUS_GATEWAY_PAYPAL" : "'.VICTORIOUS_GATEWAY_PAYPAL.'",
            "VICTORIOUS_GATEWAY_PAYPAL_PRO" : "'.VICTORIOUS_GATEWAY_PAYPAL_PRO.'",
            "VICTORIOUS_GATEWAY_PAYSIMPLE" : "'.VICTORIOUS_GATEWAY_PAYSIMPLE.'",
            "VICTORIOUS_GATEWAY_DFSCOIN" : "'.VICTORIOUS_GATEWAY_DFSCOIN.'",
            "feature_contest" : "'.__("Feature Contest", "victorious").'",
            "button_set_feature" : "'.__("Set feature", "victorious").'",
            "button_ok" : "'.__("Ok", "victorious").'",
            "button_cancel" : "'.__("Cancel", "victorious").'",
            "setting_victorious_firebase_apikey" : "'.get_option('victorious_firebase_apikey').'",
            "setting_victorious_firebase_senderid" : "'.get_option('victorious_firebase_senderid').'",
            "goliath_invalid_pass" : "'.__("Number of seleted 'pass' fixtures cannot be greater than avaiable 'pass'", "victorious").'",
            "goliath_decision_time" : "'.__("Decision Time : SPLIT OR CONTINUE", "victorious").'",
            "split" : "'.__("Split", "victorious").'",
            "continue" : "'.__("Continue", "victorious").'",
            "invite" : "'.__("Invite", "victorious").'",
            "best_inviter" : "'.__("Best inviter", "victorious").'",
            "team_each_position" : "'.__("Please select a team for each position", "victorious").'",
            "you_cannot_pick_same_team_for_same_lineup" : "'.__("You cannot pick same team for same lineup", "victorious").'",
            "you_can_only_pick_same_team_n_times" : "'.__("You can only pick same team %s times", "victorious").'",
            "cannot_pick_same_team_for_lead_team" : "'.__("Cannot pick same team for same category which is lead team", "victorious").'",
            "password" : "'.__("Password", "victorious").'",
            "withdrawal" : "'.__("Withdrawal", "victorious").'",
            "win_betting" : "'.__("WIN BETTING", "victorious").'",
            "total_points_scored" : "'.__("TOTAL POINTS SCORED", "victorious").'",
            "sportbook_error_fill_all_wagers" : "'.__("Please fill all wagers", "victorious").'", 
            "sportbook_error_empty_slip" : "'.__("Please select at least an item", "victorious").'",
            "sportbook_error_over_credit" : "'.__("Betting credits exceeded this game\'s total credit", "victorious").'",
            "sportbook_confirm_submit" : "'.__("By pressing the submit button you are consenting to our terms and conditions as well as our privacy policy. Are you sure you wish to continue?", "victorious").'",
            "update_result" : "'.__("Update Result", "victorious").'",
            "confirm_complete" : "'.__("Are you sure you want to complete contest?", "victorious").'",
            "VICTORIOUS_DEFAULT_BALANCE_TYPE_ID" : "'.VICTORIOUS_DEFAULT_BALANCE_TYPE_ID.'",
            "coin_each_position" : "'.__("Please select an entry for each remaining open slot.", "victorious").'",
            "playoff_confirm_join" : "'.esc_html(__('Are you sure you would like to join this contest?', 'victorious')).'",
            "playoff_confirm_draft_player" : "'.esc_html(__('Are you sure you want to draft this player?', 'victorious')).'",
            "playoff_confirm_remove_player" : "'.esc_html(__('Are you sure you want to remove this player? You can draft another player when draft time start', 'victorious')).'"
        }
    </script>';
}

add_action('wp_head','js_lang');

add_action('admin_enqueue_scripts', 'js_lang');