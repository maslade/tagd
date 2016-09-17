<div class="wrap">
    <h1><?php $view->page_title(); ?></h1>
    
    <?php $view->update_message(); ?>

    <form method="post">
        <?php $view->nonce(); ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="blogname">Permalink</label>
                    </th>
                    <td>
                        <em>
                            <?php $view->base_url(); ?>
                        </em>
                        <?php $view->ctl_permalink(); ?>
                        <p class="description" id="permalink-description">
                            <?php $view->permalink_help(); ?>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>

        <p class="submit"><input name="submit" id="submit" class="button button-primary" value="Save Changes" type="submit"></p></form>

</div>