<?php

require_once 'CUrlClient.php';

if(isset($_POST['user']) && isset($_POST['repo'])) {
     $curl = new CUrlClient();
     $result = $curl->send(array(
         'url' => 'https://api.github.com/repos/'. $_POST['user'].'/'. $_POST['repo'] .'/commits',
         'header' => array('User-Agent: test')
     ));

     $commits = json_decode($result, true);
     $response = array();

     if(!isset($commits['message'])) {
          $data = array();

          foreach ($commits as $commit) {
               $data[] = $commit['commit']['message'];
          }

          $form = array(
              'questions' => array(
                  array(
                      'labelAlign' => 'Auto',
                      'multipleSelections' => 'No',
                      'name' => 'input1',
                      'selected' => $data[0],
                      'options' => implode('|', $data),
                      'order' => 2,
                      'qid' => 1,
                      'required' => 'No',
                      'shuffle' => 'No',
                      'size' => -1,
                      'special' => 'None',
                      'text' => '....',
                      'type' => 'control_dropdown',
                      'visibleOptions' => 1,
                      'width' => 150
                  ),
                  array(
                      'buttonAlign' => 'Auto',
                      'buttonStyle' => 'None',
                      'clear' => 'No',
                      'clearText' => 'Clear form',
                      'name' => 'input2',
                      'order' => 3,
                      'qid' => 2,
                      'text' => 'Send',
                      'type' => 'control_button'
                  ),
                  array(
                      'headerType' => 'Default',
                      'name' => 'clickTo',
                      'order' => 1,
                      'qid' => 3,
                      'text' => 'Repository commits',
                      'textAlign' => 'Left',
                      'type' => 'control_head',
                      'verticalTextAlign' => 'Middle'
                  )
               ),
              'properties' => array(
                  'title' => 'Submission ' . date('d/m/Y H:i:s')
              )
          );

          $jotFormData = array_merge(array('apikey' => JOT_FORM_API_KEY), encodeFormParam($form));

          $result = $curl->send(array(
              'url' => 'https://api.jotform.com/form',
              'type' => 'POST',
              'data' => $jotFormData
          ));

          $result = json_decode($result, true);


          if($result['responseCode'] == 200) {
               $response['status'] = 'success';
               $response['url'] = $result['content']['url'];
          } else {
               $response['status'] = 'error';
          }
     } else {
          $response['status'] = 'error';
     }

     echo json_encode($response);
     exit;
}

function encodeFormParam($form){
     $data = array();

     foreach($form as $key => $options) {
          foreach($options as $index => $option) {
               if(is_array($option)) {
                    foreach ($option as $k => $v) {
                         $data[$key . '[' . $index . '][' . $k . ']'] = $v;
                    }
               } else {
                    $data[$key . '[' . $index . ']'] = $option;
               }
          }
     }

     return $data;
}
?>

<!DOCTYPE>
<html lang="en">
     <head>
          <meta charset="utf-8">
          <meta http-equiv="X-UA-Compatible" content="IE=edge">
          <meta name="viewport" content="width=device-width, initial-scale=1">
          <meta name="description" content="">
          <meta name="author" content="">
          <title>Test</title>

          <link href="css/bootstrap.css" rel="stylesheet">
     </head>
     <body>
          <div class="container" style="padding-top: 30px">
               <div class="row">
                    <div class="col-md-6 col-md-offset-3">
                         <form role="form">
                              <div class="alert alert-danger hidden" id="error">
                                   Invalid data
                              </div>
                              <div class="form-group">
                                   <label for="user">User</label>
                                   <input type="text" class="form-control" id="user" placeholder="Enter Username" value="laravel">
                              </div>
                              <div class="form-group">
                                   <label for="repo">Repo</label>
                                   <input type="text" class="form-control" id="repo" placeholder="Enter Repository" value="laravel">
                              </div>
                              <button type="button" class="btn btn-success" id="find">Find</button>
                         </form>
                    </div>
               </div>
          </div>
          <script type="text/javascript" src="js/jquery-1.12.1.js"></script>
          <script>
               $(document).ready(function(){
                    $('#find').click(function(e){
                         e.stopPropagation();

                         $.ajax({
                              url: '/index.php',
                              type: 'POST',
                              dataType: 'JSON',
                              data: {
                                   user: $('#user').val(),
                                   repo: $('#repo').val()
                              },
                              success: function(data) {
                                   if(data.status == 'success') {
                                        window.location.href = data.url;
                                   } else if(data.status == 'error') {
                                        $('#error').removeClass('hidden');

                                        setTimeout(function(){
                                             $('#error').addClass('hidden');
                                        }, 3000);
                                   }
                              }
                         })
                    });
               });
          </script>
     </body>
</html>
