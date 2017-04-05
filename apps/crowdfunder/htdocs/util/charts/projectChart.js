Chart.defaults.global.hover.mode = 'nearest';
Chart.defaults.global.defaultFontFamily = 'verdana';
Chart.defaults.global.defaultFontSize = 14;
Chart.defaults.global.defaultFontStyle = 'normal';
Chart.defaults.global.defaultFontColor = '#000000';

console.log("SETUP");

function imageSmoothingEnabled(ctx, state) {
    ctx.mozImageSmoothingEnabled = state;
    ctx.oImageSmoothingEnabled = state;
    ctx.webkitImageSmoothingEnabled = state;
    ctx.imageSmoothingEnabled = state;
}
function drawLineGraphWithTime( arr, label, labels, count, canvas){
  var newArr = new Array();
  for(var i =0; i < count; i++){
    newArr.push({ x: labels[i], y: arr[i]});
  }

  console.log(newArr);
  console.log(labels);
  var ctx = canvas.getContext("2d", {alpha: false});
  imageSmoothingEnabled(ctx, true);

  var data = {
    labels: labels,
    datasets: [
        {
            label: label,
            fill: false,
            lineTension: 0.1,
            backgroundColor: "rgba(75,192,192,0.4)",
            borderColor: "rgba(75,192,192,1)",
            borderCapStyle: 'butt',
            borderDash: [],
            borderDashOffset: 0.0,
            borderJoinStyle: 'miter',
            pointBorderColor: "rgba(75,192,192,1)",
            pointBackgroundColor: "#fff",
            pointBorderWidth: 1,
            pointHoverRadius: 5,
            pointHoverBackgroundColor: "rgba(75,192,192,1)",
            pointHoverBorderColor: "rgba(220,220,220,1)",
            pointHoverBorderWidth: 2,
            pointRadius: 2,
            pointHitRadius: 10,
            data: newArr,
            spanGaps: false,
        }
    ]
};

var option = {
  responsive: true,
   hover: {
     mode: 'index'
   },
   scales: {
     yAxes:[{
       display: true
     }],
     xAxes:[{
       type: 'time',
       time: {
         parser: "YYYY-MM-DD",
         unit: 'month',
         displayFormats: {
           month: 'MMM YYYY'
         }
       },
       display: true,
       ticks:{
         autoskip: true,
       }
     }]
   }
}
  var myChart = new Chart(ctx, {
    type: 'line',
    data: data,
    option: option
  });



}

function drawLineGraph( arr, label, labels, canvas){

  console.log(arr);
  console.log(labels);
  var ctx = canvas.getContext("2d", {alpha: false});
  imageSmoothingEnabled(ctx, true);

  var data = {
    labels: labels,
    datasets: [
        {
            label: label,
            fill: false,
            lineTension: 0.1,
            backgroundColor: "rgba(75,192,192,0.4)",
            borderColor: "rgba(75,192,192,1)",
            borderCapStyle: 'butt',
            borderDash: [],
            borderDashOffset: 0.0,
            borderJoinStyle: 'miter',
            pointBorderColor: "rgba(75,192,192,1)",
            pointBackgroundColor: "#fff",
            pointBorderWidth: 1,
            pointHoverRadius: 5,
            pointHoverBackgroundColor: "rgba(75,192,192,1)",
            pointHoverBorderColor: "rgba(220,220,220,1)",
            pointHoverBorderWidth: 2,
            pointRadius: 2,
            pointHitRadius: 10,
            data: arr,
            spanGaps: false,
        }
    ]
};

var option = {
  responsive: true,
   hover: {
     mode: 'index'
   },
   scales: {
     yAxes:[{
       display: true
     }],
     xAxes:[{
       display: true,
       ticks:{
         autoskip: true,
       }
     }]
   }
}
  var myChart = new Chart(ctx, {
    type: 'line',
    data: data,
    option: option
  });



}
