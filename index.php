<?php
  require "php_header.php";
?>
<!DOCTYPE html>
<html lang='ja'>
<head>
    <?php 
    //共通部分、bootstrap設定、フォントCND、ファビコン等
    include "head_bs5.php" 
    ?>
    <script src="./script/flow.js"></script>
    <TITLE><?php echo $title;?></TITLE>
    <style>
      .navbtn{
        width:100px;
      }
      table{.btn, .form-control, .form-select{
        width: 100%;
      }}
    </style>
</head>
<BODY id = 'body' style='background:black;' >
  <div id='app' style='height:100%;'>
    <HEADER style='color:#FFA400;padding-top:0;'>
      <div  class='container-fluid'>
      <div class='row'>
        <div class='col-12'>
        <nav class="navbar navbar-expand-lg bg-body-tertiary" data-bs-theme="dark">
          <div class="container-fluid">
            <a class="navbar-brand" href="index.php"><h3 class='potta-one-regular' style='color:#FFA400;'><?php echo $title;?></h3></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav me-auto mb-2 mb-lg-0">
								<li class='nav-item'>
                  <a class='nav-link' href='data_custmer.php?m=imp'>データ取込</a>
								</li>
								<li class='nav-item'>
									<a class='nav-link active' href='index.php'>入出金</a>
								</li>
								<li class='nav-item'>
									<a class='nav-link' href='data_comparison.php'>前月比・前年比</a>
								</li>
								<li class='nav-item'>
									<a class='nav-link' href='data_summary12m.php'>推移(月間・年間)</a>
								</li>
              </ul>
              <div class="d-flex" style='padding:5px;width: 240px;'>
                <input v-model='from' :disabled='search_disable' @change='read_db_meisai' type="number" class='form-control' placeholder="YYYYMM">
                <div style='padding:6px;font-weight:900;'>～</div>
                <input v-model='to' :disabled='search_disable' @change='read_db_meisai' type="number" class='form-control' placeholder="YYYYMM">
              </div>
              <!--<div class="d-flex navbtn" style='padding:5px;'>
                <input v-model='to' :disabled='search_disable' @change='read_db_meisai' type="number" class='form-control' placeholder="YYYYMM">
              </div>-->
              <div class="d-flex" style='padding:5px;'><button class='btn btn-outline-primary btn-sm navbtn' type='button' @click='cgmode'>修正モード</button></div>
              <div class="d-flex" style='padding:5px;'><button class='btn btn-outline-primary btn-sm navbtn' type='button' @click='upddata'>登録</button></div>
              <div class="d-flex" style='padding:5px;'><button class='btn btn-outline-primary btn-sm navbtn' type='button' @click='read_db_meisai'>キャンセル</button></div>
              <div class="d-flex" style='padding:5px;'>
								<a href="user_setting.php" class='a_none'><h2><i class="bi bi-person-circle"></i></h2></a>
							</div>

            </div>
          </div>
        </nav>
        </div>
      </div>
      </div>
    </HEADER>
    
    <MAIN class='container-fluid' style='color:#fff;padding-left:20px;padding-right:20px;'>
    <div class='row' style='height:100%;'>
      <div class='col-md-4 col-xl-3' style='height:100%;'>
        <div class="table-responsive table-h-full" style='width:100%;'>
          <table class="table table-hover table-sm">
            <thead class='sticky-top'>
              <tr class="table-info">
                <th scope="col">大中項目</th>
                <th scope="col">合計金額</th>
              </tr>
            </thead>
            <tbody>
              <template v-for='(list,index) in cp_readdata_summary' :key="list.daichuukou">
                <template v-if='index===0 || (index!==0 && list["daikoumoku"]!==cp_readdata_summary[index -1]["daikoumoku"])'>
                  <tr role='button' class="table-info" @click='setfilter(list["daikoumoku"],"")'>
                    <td>{{list["daikoumoku"]}}</td>
                    <td class='text-end'>{{Number(list.daikei).toLocaleString()}}</td>
                  </tr>
                </template>
                <template v-if='index!==0 && list["daikoumoku"]===cp_readdata_summary[index -1]["daikoumoku"]'>
                  <tr role='button' @click='setfilter(list["daikoumoku"],list["chuukoumoku"])'>
                    <td>　{{list["chuukoumoku"]}}</td>
                    <td class='text-end'>{{Number(list.chuukei).toLocaleString()}}</td>
                  </tr>
                </template>
              </template>
            </tbody>
            <tfoot class='sticky-bottom'>
              <tr class='table-success'>
                <td class='text-center'>入出金計</td>
                <td class='text-end'>{{Number(sum_kingaku2).toLocaleString()}}</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <div class='col-md-8 col-xl-9' style='height:100%;'>
        <div class="table-responsive table-h-full" style='width:100%;'><!---->
          <table class="table table-striped table-hover table-sm">
            <thead class='sticky-top'><!--sticky-top-->
              <tr>
                <th scope="col">#</th>
                <th class='wd100' scope="col">日付</th>
                <th scope="col">明細</th>
                <th class='wd130' scope="col">入出金額</th>
                <th scope="col">出費元</th>
                <th scope="col">大項目</th>
                <th scope="col">中項目</th>
                <th scope="col">メモ</th>
              </tr>
              <tr>
                <td role='button' @click='filter_reset'>
                  <i v-if='filseted' class="bi bi-funnel-fill" style="font-size: 1rem; color: #FFA400;" data-bs-toggle="tooltip" title="Some tooltip text!"></i>
                </td>
                <td>
                  <select v-model='fl_date' class="form-select form-select-sm wd90" placeholder="フィルタ">
                    <option value=''>ﾌｨﾙﾀ解除</option>
                    <template v-for='(list,index) in fl_date_lst' :key='list'>
                      <option :value='list'>{{list}}</option>
                    </template>
                  </select>
                </td>
                <td><input v-model='fl_meisai' type="text" class='form-control form-control-sm' placeholder="フィルタ"></td>
                <td><input v-model="fl_kin" class="form-control form-control-sm wd100" type="number" placeholder="フィルタ"></td>
                <td>
                  <select v-model='fl_shuppimoto' class="form-select form-select-sm" placeholder="フィルタ">
                    <option value=''>ﾌｨﾙﾀ解除</option>
                    <template v-for='(list,index) in fl_shuppimoto_lst' :key='list'>
                      <option :value='list'>{{list}}</option>
                    </template>
                  </select>
                </td>
                <td>
                  <select v-model='fl_dai_ko' class="form-select form-select-sm" placeholder="フィルタ">
                    <option value=''>ﾌｨﾙﾀ解除</option>
                    <template v-for='(list,index) in fl_dai_ko_lst' :key='list'>
                      <option :value='list'>{{list}}</option>
                    </template>
                  </select>
                </td>
                <td>
                  <select v-model='fl_chuu_ko' class="form-select form-select-sm" placeholder="フィルタ">
                    <option value=''>ﾌｨﾙﾀ解除</option>
                    <template v-for='(list,index) in fl_chuu_ko_lst' :key='list'>
                      <option :value='list'>{{list}}</option>
                    </template>
                  </select>
                </td>
                <td>
                  <select v-model='fl_memo' class="form-select form-select-sm" placeholder="フィルタ">
                    <option value=''>ﾌｨﾙﾀ解除</option>
                    <template v-for='(list,index) in fl_memo_lst' :key='list'>
                      <option :value='list'>{{list}}</option>
                    </template>
                  </select>
                </td>
              </tr>
              <tr>
                <td></td>
                <td>
                  <input type="date" id='cdate' class='form-control form-control-sm wd90' placeholder="一括変更" style='margin-bottom:3px;'>
                  <button type='button' class='btn btn-success btn-sm wd90' @click='hanei("cdate")'>↓反映</button>
                </td>
                <td>
                  <input type="text" id='cmeisai' class='form-control form-control-sm' placeholder="一括変更" style='margin-bottom:3px;'>
                  <button type='button' class='btn btn-success btn-sm' @click='hanei("cmeisai")'>↓反映</button>
                </td>
                <td>
                  <input type="number" id='ckin' class='form-control form-control-sm wd100' placeholder="一括変更" style='margin-bottom:3px;'>
                  <button type='button' class='btn btn-success btn-sm wd100' @click='hanei("ckin")'>↓反映</button>
                </td>
                <td>
                  <input type="text" id='cshuppi' class='form-control form-control-sm' placeholder="一括変更" style='margin-bottom:3px;'>
                  <button type='button' class='btn btn-success btn-sm' @click='hanei("cshuppi")'>↓反映</button>
                </td>
                <td>
                  <!--<input type="text" id='cdaikou' class='form-control form-control-sm' placeholder="一括変更" style='margin-bottom:3px;'>-->
                  <select id='cdaikou' class='form-select form-select-sm' placeholder="一括変更" style='margin-bottom:3px;'>
                    <option value=""></option>
                    <template v-for='(list,index) in daikoumoku_ms' :key='list.daikoumoku'>
                      <option :value=list.daikoumoku>{{list.daikoumoku}}</option>
                    </template>
                  </select>

                  <button type='button' class='btn btn-success btn-sm' @click='hanei("cdaikou")'>↓反映</button>
                </td>
                <td>
                  <input type="text" id='cchukou' class='form-control form-control-sm' placeholder="一括変更" style='margin-bottom:3px;'>
                  <button type='button' class='btn btn-success btn-sm' @click='hanei("cchukou")'>↓反映</button>
                </td>
                <td>
                  <input type="text" id='cmemo' class='form-control form-control-sm' placeholder="一括変更" style='margin-bottom:3px;'>
                  <button type='button' class='btn btn-success btn-sm' @click='hanei("cmemo")'>↓反映</button>
                </td>
              </tr>
            </thead>
            <tbody>
              <template v-for='(list,index) in readdata_filter' :key="list.SEQ">

              <tr v-if='list.b_pair_no===0' >
                  <td><button type='button' class='btn btn-outline-secondary btn-sm ps-1 pe-1 pt-0 pb-0' @click='bunkatu(index)'>
                    <i class="bi bi-arrows-expand"></i></button>
                  </td>
                  <td><input v-model='list.date' :disabled='meisai_disable' class="form-control form-control-sm wd90" type="date" placeholder=""></td>
                  <td><input @change='upd(index,"meisai")' v-model='list.fl_meisai' :disabled='meisai_disable' class="form-control form-control-sm" type="text" placeholder=""></td>
                  <td><input @change='upd(index,"kin")' v-model='list.fl_kin' :disabled='meisai_disable' class="form-control form-control-sm text-end wd100" type="number" placeholder=""></td>
                  <td><input @change='upd(index,"shuppimoto")' v-model='list.fl_shuppimoto' :disabled='meisai_disable' class="form-control form-control-sm" type="text" placeholder=""></td>
                  <td>
                    <select @change='upd(index,"daikoumoku")' v-model='list.fl_daikoumoku' :disabled='meisai_disable' class="form-select form-select-sm" placeholder="">
                      <template v-for='(list,index) in daikoumoku_ms' :key='list.daikoumoku'>
                        <option :value=list.daikoumoku>{{list.daikoumoku}}</option>
                      </template>
                    </select>
                  </td>
                  <td><input @change='upd(index,"chuukoumoku")' v-model='list.fl_chuukoumoku' :disabled='meisai_disable' class="form-control form-control-sm" type="text" placeholder=""></td>
                  <td><input @change='upd(index,"memo")' v-model='list.fl_memo' :disabled='meisai_disable' class="form-control form-control-sm" type="text" placeholder=""></td>
                </tr>

                <tr v-else-if='list.b_moto!==0' class="table-secondary">
                  <td>
                    <button v-if='list.b_moto===2' type='button' class='btn btn-outline-danger btn-sm ps-1 pe-1 pt-0 pb-0' @click='bunkatu_modosu(index)'><i class="bi bi-arrows-collapse"></i></button>
                  </td>
                  <td><input v-model='list.date' disabled class="form-control form-control-sm wd90" type="date" placeholder=""></td>
                  <td><input @change='upd(index,"meisai")' v-model='list.fl_meisai' disabled class="form-control form-control-sm" type="text" placeholder=""></td>
                  <td><input @change='upd(index,"kin")' v-model='list.fl_kin' disabled class="form-control form-control-sm text-end wd100" type="number" placeholder=""></td>
                  <td><input @change='upd(index,"shuppimoto")' v-model='list.fl_shuppimoto' disabled class="form-control form-control-sm" type="text" placeholder=""></td>
                  <td>
                    <select @change='upd(index,"daikoumoku")' v-model='list.fl_daikoumoku' disabled class="form-select form-select-sm" placeholder="">
                      <template v-for='(list,index) in daikoumoku_ms' :key='list.daikoumoku'>
                        <option :value=list.daikoumoku>{{list.daikoumoku}}</option>
                      </template>
                    </select>
                  </td>
                  <td><input @change='upd(index,"chuukoumoku")' v-model='list.fl_chuukoumoku' disabled class="form-control form-control-sm" type="text" placeholder=""></td>
                  <td><input @change='upd(index,"memo")' v-model='list.fl_memo' disabled class="form-control form-control-sm" type="text" placeholder=""></td>
                </tr>

                <tr v-else-if='list.b_pair_no!==0' class="table-info">
                  <td><!--<button type='button' class='btn btn-outline-secondary btn-sm ps-1 pe-1 pt-0 pb-0' @click='bunkatu(index)'>
                    <i class="bi bi-arrows-expand"></i></button>-->
                  </td>
                  <td><input v-model='list.date' disabled class="form-control form-control-sm wd90" type="date" placeholder=""></td>
                  <td><input @change='upd(index,"meisai")' v-model='list.fl_meisai' :disabled='meisai_disable' class="form-control form-control-sm" type="text" placeholder=""></td>
                  <td><input @change='upd(index,"kin")' v-model='list.fl_kin' disabled class="form-control form-control-sm text-end wd100" type="number" placeholder=""></td>
                  <td><input @change='upd(index,"shuppimoto")' v-model='list.fl_shuppimoto' disabled class="form-control form-control-sm" type="text" placeholder=""></td>
                  <td>
                    <select @change='upd(index,"daikoumoku")' v-model='list.fl_daikoumoku' :disabled='meisai_disable' class="form-select form-select-sm" placeholder="">
                      <template v-for='(list,index) in daikoumoku_ms' :key='list.daikoumoku'>
                        <option :value=list.daikoumoku>{{list.daikoumoku}}</option>
                      </template>
                    </select>
                  </td>
                  <td><input @change='upd(index,"chuukoumoku")' v-model='list.fl_chuukoumoku' :disabled='meisai_disable' class="form-control form-control-sm" type="text" placeholder=""></td>
                  <td><input @change='upd(index,"memo")' v-model='list.fl_memo' :disabled='meisai_disable' class="form-control form-control-sm" type="text" placeholder=""></td>
                </tr>

              </template>
            </tbody>
            <tfoot class='sticky-bottom'>
              <tr class='table-success'>
                <td></td>
                <td></td>
                <td class='text-center'>合計</td>
                <td class='text-end'>{{Number(sum_kingaku).toLocaleString()}}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
            </tfoot>
          </table>
          <button style="display: none;" type="button" data-bs-toggle='modal' data-bs-target='#meisai_bunkatu_modal' id='meisai_bunkatu_modal_open'>open</button>
        </div>
      </div>
    </div>
    <div class='logoff'><a href="logoff.php" class='a_none'><h1><i class="bi bi-box-arrow-right"></i></h1></a></div>
    </MAIN> 
    <div class='modal fade' id='meisai_bunkatu_modal' tabindex='-1' role='dialog' aria-labelledby='basicModal' aria-hidden='true'>
			<div class='modal-dialog  modal-dialog-centered modal-dialog-scrollable modal-lg'>
				<div class='modal-content edit' style=''>
					<div class='modal-header'>
	      		<h5 class="modal-title">明細分割</h5>
  	    		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id='modal_close'></button>
					</div>
					<div class='modal-body container'>
            <div class='row'>
              <table class="table table-sm">
                <thead>
                  <tr>
                    <th>明細</th>
                    <th>入出金額</th>
                    <th>大項目</th>
                    <th>中項目</th>
                    <th>メモ</th>
                  </tr>
                </thead>
                <tbody>
                  <template v-for='(list,index) in readdata_filter' :key="list.SEQ">
                    <tr v-if='index==bunkatu_index'>
                      <td>{{list.fl_meisai}}</td>
                      <td>{{list.fl_kin}}</td>
                      <td>{{list.fl_daikoumoku}}</td>
                      <td>{{list.fl_chuukoumoku}}</td>
                      <td>{{list.fl_memo}}</td>
                    </tr>
                  </template>
                  <tr>
                    <td class='text-center' colspan="5"><button type='button' class='btn btn-secondary btn-sm ps-1 pe-1 pt-0 pb-0 w80' @click='bunkatu_add(bunkatu_index)'>
                      <i class="bi bi-arrows-expand"></i></button>
                    </td>
                  </tr>
                  <template v-for='(list,index) in bunkatu_data' :key="index">
                  <tr>
                    <td><input type='text' class='form-control form-control-sm' v-model='list.fl_meisai'></td>
                    <td><input type='number' class='form-control form-control-sm' v-model='list.fl_kin'></td>
                    <td><select  class='form-select form-select-sm' v-model='list.fl_daikoumoku'>
                      <template v-for='(list,index) in daikoumoku_ms' :key='list.daikoumoku'>
                        <option :value=list.daikoumoku>{{list.daikoumoku}}</option>
                      </template>
                      </select>
                    </td>
                    <td><input type='text' class='form-control form-control-sm' v-model='list.fl_chuukoumoku'></td>
                    <td><input type='text' class='form-control form-control-sm' v-model='list.fl_memo'></td>
                  </tr>
                  </template>
                </tbody>
              </table>
            </div>
					</div>
					<div class='modal-footer'>
            <button type='button' class='btn btn-primary' @click='split_kingaku_chk'>確　定</button>
					</div>
				</div>
      </div>
    </div>
   <div class="loader-wrap" v-show='loader'>
		  <div class="loader">Loading...</div>
	  </div>
  </div>
  <script>
    //const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    //const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
  </script>
  <script src="script/dataset_vue3.js?<?php echo $time; ?>"></script>
  <script>
    dataset('<?php echo basename(__FILE__);?>').mount('#app');
  </script>
</BODY>
</html>