<div class="container">
    <div class="row owt-inner-row owt-lib-breadcrumb-top">
        <div class="col-sm-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="admin.php?page=owt-lib-manage">Library Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Manage Settings</li>
                </ol>
            </nav>
        </div>
    </div>
    <?php include_once OWT_LIBRARY_PLUGIN_DIR_PATH . 'admin/views/common.php'; ?>

    <div class="row owt-inner-row">
        <div class="panel panel-primary">
            <div class="panel-heading wpowt-lib-bg">
                Settings Panel
            </div>
            <div class="panel-body">
                <form class="form-horizontal" action="Javascript:void(0);" id="wpowt-frm-settings-panel" method="post">

                    <div class="form-group">
                        <label class="control-label col-sm-2" for="dd-currency">Currency Code*</label>
                        <div class="col-sm-4">
                            <select class="form-control" id="dd-currency" name="txt_currency">
                                <option value="-1">Select currency</option>
                                <?php
                                if (isset($params['currencies'])) {

                                    if (count($params['currencies']) > 0) {

                                        $saved_currency = get_option('owt_lib_currency_code');

                                        foreach ($params['currencies'] as $country) {
                                            $selected = '';
                                            if ($country->currency_code == $saved_currency) {
                                                $selected = 'selected="selected"';
                                            }
                                            ?>
                                            <option value="<?php echo $country->currency_code ?>" <?php echo $selected; ?>><?php echo $country->country . " ( " . $country->currency_code . ")"; ?></option>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group"> 
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-default wpowt-lib-btn"><i class="mdi mdi-check-outline"></i> Save Settings</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>