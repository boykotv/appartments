<?php 
	//обновление показаний
	function Bill($year, $month)
	{
		$cold =  $_POST["cold"];
		$hot =  $_POST["hot"];
		$gas =  $_POST["gas"];
		$light =  $_POST["light"];
		//проверка на наличие записей по месяцу
		$result = mysql_query("SELECT id FROM appartments WHERE year = '".$year."' and month = '".$month."'") or die("Invalid query: " . mysql_error());	
		$num_rows = mysql_num_rows($result);
		if ($num_rows == 0)
		{	
			//insert
			//предыдущий месяц для копирования тарифов
			if ($month == 1)
			{
				$pre_month = 12;
				$pre_year = $year - 1;
			}
			else
			{
				$pre_month = $month - 1;
				$pre_year = $year;
			}
			$query = mysql_query("SELECT id FROM appartments ORDER BY id DESC LIMIT 1") or die("Invalid query: " . mysql_error());	
			$row = mysql_fetch_array($query);
			$id = $row[0]+1; 
			
			$query = mysql_query("SELECT serv_house, phone, domophone, tv, cold_tariff, hot_tariff, stock_tariff, gas_tariff, light_tariff, light_over_tariff, warm_tariff FROM appartments WHERE year = '".$pre_year."' and month = '".$pre_month."'") or die("Invalid query: " . mysql_error());
			
			$tariffs = mysql_fetch_array($query);
			$result = mysql_query("INSERT INTO appartments  (year, month, hot, cold, gas, light, serv_house, phone,	domophone, tv, cold_tariff,	hot_tariff,
															 stock_tariff, gas_tariff, light_tariff, light_over_tariff, warm_tariff)
			values  ('".$year."', '".$month."', '".$hot."', '".$cold."', '".$gas."', '".$light."', '".$tariffs['serv_house']."', '".$tariffs['phone']."', 
					 '".$tariffs['domophone']."', '".$tariffs['tv']."', '".$tariffs['cold_tariff']."', '".$tariffs['hot_tariff']."', '".$tariffs['stock_tariff']."', 
					 '".$tariffs['gas_tariff']."', '".$tariffs['light_tariff']."', '".$tariffs['light_over_tariff']."', '".$tariffs['warm_tariff']."')") 
					 or die("Invalid query: " . mysql_error());
		}
		else
		{
			//update
			$result = mysql_query("UPDATE appartments SET cold = '$cold', hot = '$hot', gas = '$gas', light = '$light' WHERE year = '$year' AND month = '$month'") or die("Invalid query: " . mysql_error());
		}
	}
  
	//обновление тарифов
	function Tarrifs($year, $month)
	{
	   //тарифы
		$serv_house =  $_POST["serv_house"];
		$phone =  $_POST["phone"];
		$domophone =  $_POST["domophone"];
		$tv =  $_POST["tv"];
		$cold_tariff =  $_POST["cold_tariff"];
		$hot_tariff =  $_POST["hot_tariff"];
		$stock_tariff =  $_POST["stock_tariff"];
		$gas_tariff =  $_POST["gas_tariff"];
		$light_tariff =  $_POST["light_tariff"];
		$light_over_tariff =  $_POST["light_over_tariff"];
		$warm_tariff =  $_POST["warm_tariff"];
		
		$result = mysql_query("SELECT id FROM appartments WHERE year = '".$year."' and month = '".$month."'") or die("Invalid query: " . mysql_error());	
		$num_rows = mysql_num_rows($result);
		if ($num_rows == 0)
		{	
			//insert			
			$query = mysql_query("SELECT id FROM appartments ORDER BY id DESC LIMIT 1") or die("Invalid query: " . mysql_error());	
			$row = mysql_fetch_array($query);
			$id = $row[0]+1; 	
			$result = mysql_query("INSERT INTO appartments VALUES ( '".$id."', '".$year."', '".$month."', '0', '0', '0', '0', '".$serv_house."', '".$phone."', '".$domophone."', '".$tv."', '".$cold_tariff."', '".$hot_tariff."', '".$stock_tariff."', '".$gas_tariff."', '".$light_tariff."', '".$light_over_tariff."', '".$warm_tariff."')") or die("Invalid query: " . mysql_error());	
		}
		else
		{
			//update
			$row = mysql_fetch_array($result);
			$id = $row[0];	
			$result = mysql_query("UPDATE appartments SET serv_house = '$serv_house', phone = '$phone', domophone = '$domophone', tv = '$tv', cold_tariff = '$cold_tariff', hot_tariff = '$hot_tariff', stock_tariff = '$stock_tariff', gas_tariff = '$gas_tariff', light_tariff = '$light_tariff', light_over_tariff = '$light_over_tariff', warm_tariff = '$warm_tariff' WHERE id = '$id'") or die("Invalid query: " . mysql_error());	
		}	
	}

	function OutputInfo()	//ввод текущих и/или предыдущий показаний
	{
		$year = $_GET[y];
		$month = $_GET[m];
		if ($month == 1)
		{
			$pre_month = 12;
			$pre_year = $year - 1;
		}
		else
		{
			$pre_month = $month - 1;
			$pre_year = $year;
		}
		$query = mysql_query("SELECT * FROM appartments WHERE (year = $pre_year AND month = $pre_month) OR (year = $year AND month = $month) ORDER BY id") or die("Invalid query: " . mysql_error());			
		$temp = mysql_fetch_array($query);
		if ($month == $temp['month']) 
		{
			$data = $temp;
			$pre_data = mysql_fetch_array($query);
		}
		else //2 строки
		{
			$data = mysql_fetch_array($query);	
			$pre_data = $temp;		
			
			$query = mysql_query("SELECT serv_house, phone, domophone, 	tv, cold_tariff, hot_tariff, stock_tariff, gas_tariff, 	light_tariff, light_over_tariff, warm_tariff FROM appartments WHERE year = $year AND month = $month ORDER BY id") or die ("Invalid query: " . mysql_error());
			
			$tarrifs = mysql_fetch_array($query);
		}			
		?>
		<script>
			
			$(document).ready(function(){
				var y = $('select[name=years]').val();
				var m = $('select[name=months]').val();
				
				//Запись в базу показаний
				$("#form").on( "submit", function( event ){
					event.preventDefault();
					$.ajax({
						type: 'POST',
						url: 'data.php?type=0',
						data:  $( this ).serializeArray(), // send year and month
						success: function(data) {
							getData(y, m);	
						},
						error: function(jqXHR, textStatus, errorThrown) {
							console.log(errorThrown);
						}
					})
					//console.log($( this ).serializeArray());
				});
				
				//Запись в базу тарифов
				$("#tariffs").on( "submit", function( event ){
					event.preventDefault();
					params = $( this ).serializeArray();
					params.push({name: "years", value: y},{name: "months", value: m});
					$.ajax({
						type: 'POST',
						url: 'data.php?type=1',
						data: params, // send year and month
						success: function(data) {
							getData(y, m);
						},
						error: function(jqXHR, textStatus, errorThrown) {
							console.log(errorThrown);
						}
					})					
					//console.log(data);
				});	


			})			
			
			//Код для автогенерации списка и установки года и месяца
			var min = 2016,
				max = new Date().getFullYear() + 1,
				select = document.getElementsByClassName('years selectpicker')[0];
			for (var i = min; i<=max; i++)
			{
				var opt = document.createElement('option');
				opt.value = i;
				opt.innerHTML = i;
				select.appendChild(opt);
			}
			$('select[name=years]').val(<? echo $year?>);
			$('select[name=months]').val(<? echo $month?>);		
			
			//Функция выбора данных по изменению месяца и года
			$('.years, .months').on('changed.bs.select', function()
			{					
				var y = $('select[name=years]').val();
				var m = $('select[name=months]').val();
				getData(y, m);				
			});				
		</script>
		
		<?php 
			//$ip = $_SERVER['REMOTE_ADDR'];
			//if ($ip == '31.170.143.96')
			{
				?>
				<div class="container">
					<div class="row">
						<div class="col-md-6">	
							<div class="alert alert-success" id="success-alert">
								<strong>Успешно! </strong>
								Данные загружены!
							</div>	
							<form id="form" action="data.php?type=0">										
								<div id="options" class="pull-right">
									<select name="years" class="years selectpicker"></select>
									<select name="months" class="months selectpicker">
										<option value="1">январь</option>
										<option value="2">февраль</option>
										<option value="3">март</option>
										<option value="4">апрель</option>
										<option value="5">май</option>
										<option value="6">июнь</option>
										<option value="7">июль</option>
										<option value="8">август</option>
										<option value="9">сентябрь</option>
										<option value="10">октябрь</option>
										<option value="11">ноябрь</option>
										<option value="12">декабрь</option>
									</select>			
								</div>	
								
								
								<table class="table table-striped">
									<tr>
										<th></th>
										<th>было</th>
										<th></th>
										<th>стало</th>
										<th>всего</th>
										<th></th>
										<th>сумма</th>
									</tr>	
									<tr>
										<th>Телефон</th>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td>
											<?php
												echo $tarrifs['phone'];
												$tottaly .= $tarrifs['phone'];  //общий итог
											?>
										</td>
									</tr>
									<tr>
										<th>Кабельное</th>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td>
											<?php
												echo $tarrifs['tv'];
												$tottaly += $tarrifs['tv'];  //общий итог
											?>
										</td>
									</tr>
									<tr>
										<th>Содержание дома</th>
										<td></td>
										<td></td>
										<td></td>
										<td>35.43</td>
										<td></td>
										<td>
											<?php
												$res = round((35.43 * $tarrifs['serv_house']),2);
												echo $res;			//расчет по показаниям
												$tottaly += $res;   //общий итог
											?>
										</td>
									</tr>
									<tr>
										<th>Домофон</th>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td>
											<?php 
												echo $tarrifs['domophone'];
												$tottaly += $tarrifs['domophone'];  //общий итог
											?>
										</td>
									</tr>
									<tr>
										<th><a href="http://teploseti.zp.ua/ru/abonent/" target="_blank">Горячая вода</a></th>
										<td><?php if (empty($pre_data)) echo '-'; else echo $pre_data['hot'];?></td>
										<td>-</td>
										<td class="has-success"><input type="text" pattern="^[ 0-9]+$" name="hot" class="form-control" value="<?php if ($data['hot'] == 0) $data['hot'] = ""; echo $data['hot'];?>"/></td>
										<td>
											<?php
												$diff = -1;
												if ($data['hot'] != "" && $pre_data['hot'] != "") 
												{													
													$diff = $data['hot'] - $pre_data['hot'];
													echo $diff;
												}
											?>
										</td>
										<td></td>
										<td>
											<?php
												if ($diff <0)
												{
													echo '-';	//либо разница отрицательная, либо не заполнено значение текущего или прошлого месяца
												}
												else
												{
													$res = round(($diff * $tarrifs['hot_tariff']), 2);
													echo $res;			//расчет по показаниям
													$tottaly += $res;   //общий итог
												}
											?>
										</td>
									</tr>	
									<tr>
										<th><a href="http://www.vodokanal.zp.ua/entry" target="_blank">Холодная вода</a></th>
										<td><?php if (empty($pre_data)) echo '-'; else echo $pre_data['cold'];?></td>
										<td>-</td>
										<td class="has-success"><input type="text" pattern="^[ 0-9]+$" name="cold" class="form-control" value="<?php if ($data['cold'] == 0) $data['cold'] = ""; echo $data['cold'];?>"/></td>
										<td>
											<?php
												if ($data['cold'] != "" && $pre_data['cold'] != "") 
												{
													$diff = $data['cold'] - $pre_data['cold'];
													echo $diff;
												}
											?>
										</td>
										<td></td>
										<td>
											<?php
												if ($data['cold'] != "" && $pre_data['cold'] != "") 
												{
													if ($data['hot'] != "" && $pre_data['hot'] != "") 
													{														
														//расчет полный														
														$res = round(($diff * $tarrifs['cold_tariff'] + $diff * $tarrifs['stock_tariff'] +  $tarrifs['stock_tariff'] * ($data['hot'] - $pre_data['hot'])), 2);
														echo $res;			//расчет по показаниям
														$tottaly += $res;   //общий итог
													}	
													else
													{
														//расчет по хв
														$res = round(($diff * $tarrifs['cold_tariff'] + $diff * $tarrifs['stock_tariff']), 2);
														echo $res;			//расчет по показаниям
														$tottaly += $res;   //общий итог
													}
												}
												else
												{
													if ($data['hot'] != "" && $pre_data['hot'] != "") 
													{
														//расчет по гв														
														$res = round(($tarrifs['stock_tariff'] * ($data['hot'] - $pre_data['hot'])), 2);
														echo $res;			//расчет по показаниям
														$tottaly += $res;   //общий итог
													}
													else
													{
														echo '-';	//нет данных для расчета хв, стоки
													}													
												}
											?>
										</td>
									</tr>
									<tr>
										<th><a href="https://104.ua/ua/cabinet/info" target="_blank">Газ</a></th>
										<td><?php if (empty($pre_data)) echo '-'; else echo $pre_data['gas'];?></td>
										<td>-</td>
										<td class="has-success"><input type="text" pattern="^[ 0-9]+$" name="gas" class="form-control" value="<?php if ($data['gas'] == 0) $data['gas'] = ""; echo $data['gas'];?>"/></td>
										<td>
											<?php
												$diff = -1;
												if ($data['gas'] != 0 && $pre_data['gas'] != 0) 
												{
													$diff = $data['gas'] - $pre_data['gas'];
													echo $diff;
												}
											?>
										</td>
										<td></td>
										<td>
											<?php
												if ($diff <0)
												{
													echo '-';	//либо разница отрицательная, либо не заполнено значение текущего или прошлого месяца
												}
												else	
												{
													$res = round(($diff * $tarrifs['gas_tariff']), 2);
													echo $res;			//расчет по показаниям					
													$tottaly += $res;   //общий итог
												}
											?>
										</td>
									</tr>	
									<tr>
										<th><a href="http://www.zoe.com.ua/pokazania.php" target="_blank">Свет</a></th>
										<td><?php if (empty($pre_data)) echo '-'; else echo $pre_data['light'];?></td>
										<td>-</td>
										<td class="has-success"><input type="text" pattern="^[ 0-9]+$" name="light" class="form-control" value="<?php if ($data['light'] == 0) $data['light'] = ""; echo $data['light'];?>"/></td>
										<td>
											<?php
												$diff = -1;
												if ($data['light'] != 0 && $pre_data['light'] != 0) 
												{
													$diff = $data['light'] - $pre_data['light'];
													echo $diff;
												}
											?>
										</td>
										<td></td>
										<td>
											<?php
												if ($diff <0)
												{
													echo '-';	//либо разница отрицательная, либо не заполнено значение текущего или прошлого месяца
												}
												else
												{
													if ($diff > 100)
													{
														$res = round((100 * $tarrifs['light_tariff']), 2) + round((($diff - 100) * $tarrifs['light_over_tariff']), 2);
														$res = floor($res*1.2*100)/100;
														echo $res;			//расчет по показаниям
														$tottaly += $res;   //общий итог
													}
													else 
													{
														$res = round(($diff * $tarrifs['light_tariff']), 2);
														$res = floor($res*1.2*100)/100;
														echo $res;			//расчет по показаниям
														$tottaly += $res;   //общий итог														
													}
												}
											?>
										</td>
									</tr>
									<tr>
										<th>Отопление</th>
										<td></td>
										<td></td>
										<td></td>
										<td>34,5</td>
										<td></td>
										<td>
											<?php
												$res = round((34.5 * $tarrifs['warm_tariff']), 2);
												echo $res;			//расчет по показаниям
												$tottaly += $res;   //общий итог
												
											?>
										</td>
									</tr>	
									<tr class="success">
										<th>ИТОГО</th>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<th>
											<?php
												echo $tottaly;
											?>
										</th>
									</tr>								
								</table>	
								<input type="submit" class="btn btn-success btn-lg btn-block" value="Сохранить показания"/>								
							</form>	
						</div>
						<div class="col-md-4">
							<form id="tariffs" action="data.php?type=1">
								<table class="table table-striped" >
									<tr>
										<td>Содержание дома</td>
										<td class="has-success"><input type="text" pattern="[+-]?([0-9]+([.][0-9]*)?|[.][0-9]+)" name="serv_house" class="form-control" value="<?php echo $tarrifs['serv_house'];?>"/></td>
									</tr>
									<tr>
										<td>Телефон</td>
										<td class="has-success"><input type="text" pattern="[+-]?([0-9]+([.][0-9]*)?|[.][0-9]+)" name="phone" class="form-control" value="<?php echo $tarrifs['phone'];?>"/></td>
									</tr>
									<tr>
										<td>Домофон</td>
										<td class="has-success"><input type="text" pattern="[+-]?([0-9]+([.][0-9]*)?|[.][0-9]+)" name="domophone" class="form-control" value="<?php echo $tarrifs['domophone'];?>"/></td>
									</tr>
									<tr>
										<td>Кабельное</td>
										<td class="has-success"><input type="text" pattern="[+-]?([0-9]+([.][0-9]*)?|[.][0-9]+)" name="tv" class="form-control" value="<?php echo $tarrifs['tv'];?>"/></td>
									</tr>
									<tr>
										<td>Холодная вода</td>
										<td class="has-success"><input type="text" pattern="[+-]?([0-9]+([.][0-9]*)?|[.][0-9]+)" name="cold_tariff" class="form-control" value="<?php echo $tarrifs['cold_tariff'];?>"/></td>
									</tr>	
									<tr>
										<td>Горячая вода</td>
										<td class="has-success"><input type="text" pattern="[+-]?([0-9]+([.][0-9]*)?|[.][0-9]+)" name="hot_tariff" class="form-control" value="<?php echo $tarrifs['hot_tariff'];?>"/></td>
									</tr>
									<tr>
										<td>Стоки</td>
										<td class="has-success"><input type="text" pattern="[+-]?([0-9]+([.][0-9]*)?|[.][0-9]+)" name="stock_tariff" class="form-control" value="<?php echo $tarrifs['stock_tariff'];?>"/></td>
									</tr>
									<tr>
										<td>Газ</td>
										<td class="has-success"><input type="text" pattern="[+-]?([0-9]+([.][0-9]*)?|[.][0-9]+)" name="gas_tariff" class="form-control" value="<?php echo $tarrifs['gas_tariff'];?>"/></td>
									</tr>
									<tr>
										<td>Свет &lt; 100</td>
										<td class="has-success"><input type="text" pattern="[+-]?([0-9]+([.][0-9]*)?|[.][0-9]+)" name="light_tariff" class="form-control" value="<?php echo $tarrifs['light_tariff'];?>"/></td>
									</tr>
									<tr>
										<td>Свет &gt; 100</td>
										<td class="has-success"><input type="text" pattern="[+-]?([0-9]+([.][0-9]*)?|[.][0-9]+)" name="light_over_tariff" class="form-control" value="<?php echo $tarrifs['light_over_tariff'];?>"/></td>
									</tr>
									<tr>
										<td>Отопление</td>
										<td class="has-success"><input type="text" pattern="[+-]?([0-9]+([.][0-9]*)?|[.][0-9]+)" name="warm_tariff" class="form-control" value="<?php echo $tarrifs['warm_tariff'];?>"/></td>
									</tr>
								</table>
								<input id="tariff" type="submit" class="btn btn-success btn-lg btn-block" value="Сохранить тарифы"/>
							</form>
						</div>
					</div>
				</div>
				<?php
			}
			/*else
			{
				?>
					<div class="container container-table">
						<div class="row vertical-center-row">
							<div class="text-center col-md-4 col-md-offset-4 rejected"><b>Доступ запрещен</b></div>
						</div>
					</div>
				<?
			}*/

		?>
	<?
	}
  
  
	$db_name="bbird_appart";  
	$host = "bbird.mysql.ukraine.com.ua"; 
	$user = "bbird_appart"; 
	$pswd = "91t6y4iz";
	$dbh = mysql_connect($host, $user, $pswd) or die("Не могу соединиться с MySQL.");
	mysql_select_db($db_name) or die("Не могу подключиться к базе.");

	$type= $_GET["type"];
	$year =  $_POST["years"];
	$month =  $_POST["months"];

	if ($type == 0)	Bill($year, $month); 		//передача показаний
	elseif ($type == 1) Tarrifs($year, $month); //передача тарифов
	else OutputInfo(); 							//вывод и анализ данных
	mysql_close($dbh);
?>