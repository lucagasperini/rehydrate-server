<body onload="onload_home()">
        <div class="main">
                <h1>
                        <?php echo _('header_home'); ?>
                </h1>
                <input id="drink_quantity" type="number" value="200" style="display:none;" />
                <button id="drink_button" onclick="onclick_drink_button()">
                        <?php echo _('drink_button'); ?>
                </button>

                <input type="datetime-local" id="drink_time"/>
                <label for="drink_time">
                        <?php echo _('label_drink_time'); ?>
                </label>



                <h2>
                        <?php echo _('header_today'); ?>
                </h2>
                <canvas class="chart" id="chart_today"></canvas>



                <h2>
                        <?php echo _('header_history'); ?>
                </h2>
                <h4>
                        <?php echo _('history_time_header'); ?>
                </h4>
                <label for="history_start_time">
                        <?php echo _('label_history_start_time'); ?>
                </label>

                <input type="date" id="history_start_time" onchange="set_chart_history_time()" />
                <label for="history_end_time">
                        <?php echo _('label_history_end_time'); ?>
                </label>
                <input type="date" id="history_end_time" onchange="set_chart_history_time()" />
                <h4>
                        <?php echo _('history_mode_header'); ?>
                </h4>
                <button class="button" id="history_button_hourly" onclick="set_chart_history_mode(0)">
                        <?php echo _('history_mode_hourly'); ?>
                </button>
                <button class="button" id="history_button_daily" onclick="set_chart_history_mode(1)">
                        <?php echo _('history_mode_daily'); ?>
                </button>
                <button class="button" id="history_button_weekly" onclick="set_chart_history_mode(2)">
                        <?php echo _('history_mode_weekly'); ?>
                </button>
                <button class="button" id="history_button_monthly" onclick="set_chart_history_mode(3)">
                        <?php echo _('history_mode_mounthly'); ?>
                </button>
                <button class="button" id="history_button_yearly" onclick="set_chart_history_mode(4)">
                        <?php echo _('history_mode_yearly'); ?>
                </button>

                <canvas class="chart" id="chart_history"></canvas>

                <button id="drink_button" onclick="onclick_disconnect_button()">
                        <?php echo _('disconnect_button'); ?>
                </button>
        </div>
</body>