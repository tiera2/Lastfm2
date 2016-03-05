      <!-- Main component for a primary marketing message or call to action -->
      <div class="panel panel-default">
        <!-- Default panel contents -->
        <div class="panel-heading"><?php if($image != null) echo "<img src='$image'>";echo $panel_heading;  ?></div>

        <!-- Table -->
        <table class="table table-striped">
          <thead>
            <tr>
          <?php 
          /** LOOP HEADERS **/
            foreach($headers AS $header) {
          ?>
          <th>
          <?php
            echo $header;
          ?>
        </th>
        <?php
            }
          ?>
        </tr>
        </thead>
          <tbody>
          <?php 
          /** LOOP HEADERS **/
            foreach($tablerows AS $tablerow) {
          ?>
          
          <?php
            if(isset($tablerow['diff']) && $tablerow['diff'] > 0) {
              echo '<tr class="success">';
            } else if (isset($tablerow['diff']) && $tablerow['diff'] < 0) {
              echo '<tr class="danger">';
            } else {
              echo '<tr>';
            }
            foreach($tablerow as $el) {
              echo "<td>$el</td>";
            }
            
          ?>
        </tr>
        <?php
            }
          ?>
        </tbody>
        </table>

        <div ng-app="stryktApp">
          <div ng-controller="stryktController">
            <div id="content"> <h1>Välkommen! {{ message }}</h1> </div>
          </div>
        </div>
		<!--div ng-app="myApp">
<div ng-controller="PeopleCtrl">
    <p>    Click <a ng-click="loadPeople()">here</a> to load data.</p>
<table>
    <tr>
        <th>Id</th>
        <th>First Name</th>
        <th>Last Name</th>
    </tr>
    <tr ng-repeat="person in people">
        <td>{{person.id}}</td>
        <td>{{person.firstName}}</td>
        <td>{{person.lastName}}</td>
    </tr>
</table>
</div>
</div-->

      </div>