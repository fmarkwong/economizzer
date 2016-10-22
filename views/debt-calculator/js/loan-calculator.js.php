$(function(){
    $('#term-date-picker input').datepicker({
        format: "yyyy/mm/dd",
        todayHighlight: true,
        startDate: new Date() 
    });
});

var dateTerm = false;

var termDefaultValue = dateTerm ? $.format.date(new Date(), 'yyyy-MM-dd') : '36';


$(".calculator-loan").accrue({
  mode: "basic",
  dateTerm: dateTerm,
  operation: "keyup",
  default_values: {
    amount: "7500",
    rate: "7%",
    rate_compare: "1.49%",
    term: termDefaultValue, 
  },
  field_titles: {
    amount: "Loan Amount",
    rate: "Yearly Interest Rate",
    rate_compare: "Comparison Rate",
    term: "Months To Pay"
  },
  button_label: "Calculate",
  field_comments: {
    amount: "",
    rate: "",
    rate_compare: "",
    term: ""
  },
  response_output_div: ".results",
  response_basic: 
    '<p><strong>Monthly Payment:</strong><br>%payment_amount%</p>'+
    '<p><strong>Number of Payments:</strong><br>%num_payments%</p>'+
    '<p><strong>Total Payment:</strong><br>%total_payments%</p>'+
    '<p><strong>Total Interest:</strong><br>%total_interest%</p>',
  response_compare: "Save $%savings% in interest!",
  error_text: "Please fill in all fields.",
  callback: function ( elem, data ){
  }
});


// $(".calculator-amortization").accrue({
//   mode: "amortization"
// });
