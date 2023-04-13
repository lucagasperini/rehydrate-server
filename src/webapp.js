
function get_cookie(cname) {
        let name = cname + "=";
        let decodedCookie = decodeURIComponent(document.cookie);
        let ca = decodedCookie.split(';');
        for(let i = 0; i <ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) == ' ') {
                        c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                        return c.substring(name.length, c.length);
                }
        }
        return "";
}

function set_cookie(cname, cvalue, exdays) {
        const d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        let expires = "expires="+ d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function delete_cookie(cname)
{
       set_cookie(cname,'',-1);
}

async function auth_call(user, pass) {
        const response = await fetch('auth.php', {
                method: 'POST',
                body: new URLSearchParams({u: user, p: pass}),
                mode: "cors", 
                cache: "no-cache", 
                credentials: "same-origin",
                redirect: "error", 
                referrerPolicy: "no-referrer",

                headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                }
        });

        if(response.ok) {
                return response.text();
        } else {
                console.error(response.status);
                return false;
        }
}

async function rest_call(data = {}) {
        const response = await fetch('rest.php', {
                method: 'POST',
                body: new URLSearchParams(data),
                mode: "cors",
                cache: "no-cache",
                credentials: "same-origin",
                redirect: "error",
                referrerPolicy: "no-referrer",
                headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                }
        });

        if(response.ok) {
                return response.json(); //extract JSON from the http response
        } else {
                if(response.status === 401) {
                        delete_cookie("token");
                        window.location.reload();
                }
                return false;
        }
}

async function rest_send(quantity, time) {
        return rest_call({ token: get_cookie("token"), type: "send", quantity: quantity, time: time });
}

async function rest_receive_today() {
        return { 
                record: await rest_call({ token: get_cookie("token"), type: "receive", time_start: "today", sum: "hourly" }),
                future: await rest_call({ token: get_cookie("token"), type: "plan"})
        }
}
async function rest_receive_history(mode, time_start = null, time_end = null) {
        if (mode === 0) {
                return await rest_call({ token: get_cookie("token"), type: "receive", time_start: time_start, time_end: time_end, sum: "hourly" });
        } else if(mode === 1) {
                return await rest_call({ token: get_cookie("token"), type: "receive", time_start: time_start, time_end: time_end, sum: "daily" });
        } else if (mode === 2) {
                return await rest_call({ token: get_cookie("token"), type: "receive", time_start: time_start, time_end: time_end, sum: "weekly" });
        } else if (mode === 3) {
                return await rest_call({ token: get_cookie("token"), type: "receive", time_start: time_start, time_end: time_end, sum: "monthly" });
        } else if (mode === 4) {
                return await rest_call({ token: get_cookie("token"), type: "receive", time_start: time_start, time_end: time_end, sum: "yearly" });
        } else {
                console.error("Sum mode of history is invalid");
        }
}

function onclick_drink_button() {
        time = document.getElementById("drink_time").value;
        
        if(time == "") {
                epoch_time = Math.floor(new Date().getTime() / 1000);
        } else {
                epoch_time = Math.floor(new Date(time).getTime() / 1000);
        }
        rest_send(document.getElementById("drink_quantity").value, epoch_time);
        onload_home();
}



function init_chart_day(name, data, type = "bar") {
        var loaded_graph = Chart.getChart(name);
        if(loaded_graph) {
                loaded_graph.destroy();
        }

        var x_values = [
                "00:00",
                "01:00",
                "02:00",
                "03:00",
                "04:00",
                "05:00",
                "06:00",
                "07:00",
                "08:00",
                "09:00",
                "10:00",
                "11:00",
                "12:00",
                "13:00",
                "14:00",
                "15:00",
                "16:00",
                "17:00",
                "18:00",
                "19:00",
                "20:00",
                "21:00",
                "22:00",
                "23:00"
        ];
        var j = 0;
        var y_values_history = [];
        for (let i = 0; i < x_values.length; i++) {
                const element = data.record[j];
                
                if(element && element.date.slice(11) === x_values[i]) {
                        y_values_history[i] =  element.quantity;
                        j++;
                } else {
                        y_values_history[i] = 0;
                }
        }

        var y_values_plan = [];
        j = 0;
        for (let i = 0; i < x_values.length; i++) {
                const element = data.future.plan[j];
                
                if(element && element.date === x_values[i]) {
                        y_values_plan[i] =  element.quantity;
                        j++;
                } else {
                        y_values_plan[i] = 0;
                }
        }

        new Chart(name, {
          type: type,
          data: {
            label: null,
            labels: x_values,
            datasets: [{ 
              data: y_values_history,
              borderColor: "cyan",
              backgroundColor: "cyan",
              fill: false
            }, { 
                data: y_values_plan,
                borderColor: "magenta",
                backgroundColor: "magenta",
                fill: false
            }]
          },
          options: {
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        
        });
}

function init_chart_history(name, data, type = "line") {
        var loaded_graph = Chart.getChart(name);
        if(loaded_graph) {
                loaded_graph.destroy();
        }

        var x_values = data.map(function(item){
                if('date' in item) {
                        return item.date;
                } else if ('time' in item) {
                        return (new Date(item.time * 1000)).toLocaleString(); //rest api provide epoch seconds, js wants milliseconds 
                } else if('plan' in item) {
                        return item.plan;
                } else {
                        console.error("Cannot load this type of data");
                }
        });

        var y_values = data.map(function(item){return item.quantity;});

        new Chart(name, {
          type: type,
          data: {
            labels: x_values,
            datasets: [{ 
              data: y_values,
              borderColor: "cyan",
              fill: false
            }]
          },
          options: {
                plugins: {
                    legend: {
                        display: false,
                    }
                }
            }
        
        });
}

function get_history_mode() {
        var history_mode = localStorage.getItem("history_mode");
        if(history_mode === null) {
                return 1; // default is daily
        } else {
                return Number(history_mode);
        }
}

function onload_home() {
        Chart.defaults.backgroundColor="cyan";
        Chart.defaults.borderColor="cyan";
        Chart.defaults.color="cyan";
        rest_receive_today().then(
                data => { 
                        //take first plan
                        var first_plan = data.future.plan[0]["date"];
                        var notify_hour = first_plan.slice(0, first_plan.length - 3);
                        localStorage.setItem("notify_hour", notify_hour);
                        init_chart_day("chart_today", data, "bar");
                }
        );
        
        rest_receive_history(get_history_mode()).then(
                data => init_chart_history("chart_history", data)
        );

        var timer_notify = function() {
                var notify_hour = Number(localStorage.getItem("notify_hour"));
                const d = new Date().getHours();
                if(notify_hour == d) {
                        notify_me("Drink water!");
                        window.setTimeout(timer_notify, 1000 * 60 * 10); // 10 min
                }
        }

        window.setTimeout(timer_notify, 1000);
        
}

function do_notify(text) {
        const notification = new Notification(text);
        var snd = new Audio("sounds/Enharpment.ogg");
        snd.play();
}

function notify_me(text) {
        if (!("Notification" in window)) {
          // Check if the browser supports notifications
          alert("This browser does not support desktop notification");
        } else if (Notification.permission === "granted") {
          // Check whether notification permissions have already been granted;
          // if so, create a notification
          do_notify(text);
        } else if (Notification.permission !== "denied") {
          // We need to ask the user for permission
          Notification.requestPermission().then((permission) => {
            // If the user accepts, let's create a notification
            if (permission === "granted") {
                do_notify(text);
            }
          });
        }
      
        // At last, if the user has denied notifications, and you
        // want to be respectful there is no need to bother them anymore.
}
      

function set_chart_history_mode(mode) {
        localStorage.setItem("history_mode", mode);
        rest_receive_history(mode).then(
                data => init_chart_history("chart_history", data)
        );
}

function set_chart_history_time() {
        var history_start_time = new Date(
                document.getElementById("history_start_time").value
        ).getTime() / 1000;
        var history_end_time = new Date(
                document.getElementById("history_end_time").value
        ).getTime() / 1000;

        rest_receive_history(get_history_mode(), history_start_time, history_end_time).then(
                data => init_chart_history("chart_history", data)
        );
}

function auth_web() {
        var login_user = document.getElementById("login_user").value;
        var login_pass = document.getElementById("login_pass").value;
        auth_call(login_user, login_pass).then(
                data => { set_cookie("token", data, 365); window.location.reload(); }
        );
}

function onclick_disconnect_button() {
        delete_cookie("token");
        window.location.reload();
}