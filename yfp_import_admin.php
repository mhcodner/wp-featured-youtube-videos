<?php
    if($_POST['yfp_hidden'] == 'Y') {
        //Form data sent
        $yfp_URL = $_POST['yfp_URL'];
        update_option('yfp_URL', $yfp_URL);
        $yfp_1_name = $_POST['yfp_1_name'];
        update_option('yfp_1_name', $yfp_1_name);
        $yfp_URL_1 = $_POST['yfp_URL_1'];
        update_option('yfp_URL_1', $yfp_URL_1);
        $yfp_2_name = $_POST['yfp_2_name'];
        update_option('yfp_2_name', $yfp_2_name);
        $yfp_URL_2 = $_POST['yfp_URL_2'];
        update_option('yfp_URL_2', $yfp_URL_2);
        ?>
        <div class="updated"><p><strong><?php _e('Options saved.' ); ?></strong></p></div>
        <?php
    } else {
        //Normal page display
        $yfp_URL = get_option('yfp_URL');
        $yfp_1_name = get_option('yfp_1_name');
        $yfp_URL_1 = get_option('yfp_URL_1');
        $yfp_2_name = get_option('yfp_2_name');
        $yfp_URL_2 = get_option('yfp_URL_2');
    }
?>
<div class="wrap">
  <h2>Configure the YouTube playlists to be used</h2>
  <form name="yfp_form" method="post" enctype="multipart/form-data" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
    <input type="hidden" name="yfp_hidden" value="Y">
    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th scope="row">
            <label for="yfp_URL">Featured YouTube playlist address</label>
          </th>
          <td>
            <input type="text" class="regular-text code" name="yfp_URL" value="<?php echo $yfp_URL; ?>" >
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="yfp_1">YouTube playlist</label>
          </th>
          <td>
            <p><input type="text" class="regular-text code" name="yfp_1_name" placeholder="Playlist name" value="<?php echo $yfp_1_name; ?>" ></p>
            <p><input type="text" class="regular-text code" name="yfp_URL_1" placeholder="Playlist address" value="<?php echo $yfp_URL_1; ?>" ></p>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="yfp_2">YouTube playlist</label>
          </th>
          <td>
            <p><input type="text" class="regular-text code" name="yfp_2_name" placeholder="Playlist name" value="<?php echo $yfp_2_name; ?>" ></p>
            <p><input type="text" class="regular-text code" name="yfp_URL_2" placeholder="Playlist address" value="<?php echo $yfp_URL_2; ?>" ></p>
          </td>
        </tr>
      </tbody>
    </table>
    <p class="submit">
      <input type="submit" class="button button-primary" name="Submit" value="Save Changes" />
    </p>
  </form>
</div>