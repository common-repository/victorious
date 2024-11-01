<div id="dlgUploadPhoto" style="display: none;">
    <div id="msgUploadPhoto" class="public_message"></div>
    <form id="formUploadPhoto">
        <input type="hidden" name="action" value="saveUploadPhotoResult"/>
        <input type="hidden" name="league_id" value="<?php echo esc_attr($league['leagueID']);?>"/>
        <input type="hidden" name="image"/>
        <table>
            <tr>
                <td style="width:20%">
                    <?php echo esc_html(__("Fixture", 'victorious'));?>
                </td>
                <td>
                    <select name="fixture_id">
                        <option value=""><?php echo esc_html(__("Select fixture", 'victorious'));?></option>
                        <?php foreach($fights as $fight):?>
                            <option value="<?php echo esc_attr($fight['fightID']);?>"><?php echo esc_html($fight['name']).': '.VIC_DateTranslate($fight['startDate']);?></option>
                        <?php endforeach;?>
                    </select>
                </td>
            </tr>
            <tr>
                <td style="width:20%">
                    <?php echo esc_html(__("Total Kills", 'victorious'));?>
                </td>
                <td>
                    <input type="text" name="total_kill"/>
                </td>
            </tr>
            <tr>
                <td><?php echo esc_html(__("Finish", 'victorious'));?></td>
                <td>
                    <input type="text" name="finish"/>
                </td>
            </tr>
            <tr>
                <td><?php echo esc_html(__("Upload Photo", 'victorious'));?></td>
                <td>
                    <div id="uploadPhotoImage"></div>
                </td>
            </tr>
        </table>
    </form>
</div>