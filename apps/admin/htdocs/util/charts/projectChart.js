var ctx = document.getElementById("myChart");
Chart.defaults.global.hover.mode = 'nearest';
console.log("SETUP");
function drawGraph(){
  console.log(isNaN(a));
  if(isNaN(a)){
    a=0;
  }
  if(isNaN(b)){
    b=0;
  }
  if(isNaN(c)){
    c=0;
  }
  if(isNaN(d)){
    d=0;
  }
  if(isNaN(e)){
    e=0;
  }

  d+=e;
  c+=d;
  b+=c;
  a+=b;
  var data = {
    labels: ["4 weeks ago", "2 weeks ago" , "2 weeks ago" , "1 week ago" , "This week"],
    datasets: [
        {
            label: "$ raised the last month",
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
            data: [e,d,c,b,a],
            spanGaps: false,
        }
    ]
};

var option = {
   hover: {
     mode: 'index'
   },
   scales: {
     yAxes:[{
       ticks:{
         min: 10000
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
