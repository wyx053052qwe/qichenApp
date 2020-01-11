Vue.component('com-citychoice',{
	data:function(){
		return {
			isCancel:false,
			zimShow:false,
			ssval:'',
			zimText:'',
			sidabers:[
				'热门','A','B','C','D','E','F','G','H','J','K','L','M','N','P','Q','R','S','T','W','X','Y','Z'
			],
			citys:[
				{city:'南宁',py:'nanning'},{city:'桂林',py:'guilin'},{city:'百色',py:'baise'},{city:'北海',py:'beihai'},{city:'崇左',py:'chongzuo'},
				{city:'防城港',py:'fangchenggang'},{city:'贵港',py:'guigang'},{city:'河池',py:'hechi'},{city:'贺州',py:'hezhou'},
				{city:'来宾',py:'laibin'},{city:'柳州',py:'liuzhou'},{city:'钦州',py:'qinzhou'},{city:'梧州',py:'wuzhou'},{city:'玉林',py:'yulin'},
				{city:'武鸣县',py:'wumingxian'},{city:'隆安县',py:'longanxian'},{city:'马山县',py:'mashanxian'},{city:'上林县',py:'shanglinxian'},
				{city:'宾阳县',py:'binyangxian'},{city:'横县',py:'hengxian'},{city:'阳朔县',py:'yangshuoxian'},{city:'临桂县',py:'linguixian'},
				{city:'灵川县',py:'lingchuanxian'},{city:'全州县',py:'quanzhouxian'},{city:'平乐县',py:'pinglexian'},{city:'兴安县',py:'xinganxian'},
				{city:'灌阳县',py:'guanyangxian'},{city:'荔浦县',py:'lipuxian'},{city:'资源县',py:'ziyuanxian'},{city:'永福县',py:'yongfuxian'},
				{city:'龙胜',py:'longsheng'},{city:'恭城',py:'gongcheng'},{city:'凌云县',py:'lingyunxian'},
				{city:'平果县',py:'pingguoxian'},{city:'西林县',py:'xilinxian'},{city:'乐业县',py:'leyexian'},
				{city:'德保县',py:'debaoxian'},{city:'田林县',py:'tianlinxian'},{city:'田阳县',py:'tianyangxian'},{city:'靖西县',py:'jingxixian'},
				{city:'田东县',py:'tiandongxian'},{city:'那坡县',py:'napoxian'},{city:'隆林',py:'longlin'},{city:'合浦县',py:'hepuxian'},
				{city:'凭祥市',py:'pingxiangshi'},{city:'宁明县',py:'ningmingxian'},{city:'扶绥县',py:'fusuixian'},{city:'龙州县',py:'longzhouxian'},
				{city:'大新县',py:'daxinxian'},{city:'天等县',py:'tiandengxian'},{city:'东兴市',py:'dongxingshi'},{city:'上思县',py:'shangsixian'},
				{city:'桂平市',py:'guipingshi'},{city:'平南县',py:'pingnanxian'},{city:'宜州市',py:'yizhoushi'},{city:'天峨县',py:'tianexian'},
				{city:'凤山县',py:'fengshanxian'},{city:'南丹县',py:'nandanxian'},{city:'东兰县',py:'donglanxian'},
				{city:'都安',py:'duan'},{city:'罗城',py:'luocheng'},{city:'巴马',py:'bama'},{city:'环江',py:'huanjiang'},
				{city:'大化',py:'dahua'},{city:'钟山县',py:'zhongshanxian'},{city:'昭平县',py:'zhaopingxian'},{city:'富川',py:'fuchuan'},
				{city:'合山市',py:'heshanshi'},{city:'象州县',py:'xiangzhouxian'},{city:'武宣县',py:'wuxuanxian'},{city:'忻城县',py:'xinchengxian'},{city:'金秀',py:'jinxiu'},
				{city:'柳江县',py:'liujiangxian'},{city:'柳城县',py:'liuchengxian'},{city:'鹿寨县',py:'luzhaixian'},{city:'融安县',py:'ronganxian'},
				{city:'融水',py:'rongshui'},{city:'三江',py:'sanjiang'},{city:'灵山县',py:'lingshanxian'},{city:'浦北县',py:'pubeixian'},{city:'岑溪市',py:'cenxishi'},
				{city:'苍梧县',py:'cangwuxian'},{city:'藤县',py:'tengxian'},{city:'蒙山县',py:'mengshanxian'},{city:'北流市',py:'beiliushi'},
				{city:'容县',py:'rongxian'},{city:'陆川县',py:'luchuanxian'},{city:'博白县',py:'bobaixian'},{city:'兴业县',py:'xingyexian'},{city:'深圳',py:'shenzhen'}
			],
			hostCitys:[]
		}
	},
	template:`
		<section class="cityChoiceBox" id="cityChoiceBox">
			<article class="cityChoice-top transit" v-bind:class='{focus:isCancel}'>
				<transition enter-active-class="animated fadeInLeft" leave-active-class="animated fadeOutLeft">
					<span class="fa fa-angle-left" v-if='!isCancel' v-on:click='cityClose'></span>
				</transition>
				<input type="text" class="search-input" placeholder="中文/拼音/首写字母" v-model.trim='ssval' v-on:focus='search' />
				<transition enter-active-class="animated fadeInRight" leave-active-class="animated fadeOutRight">
					<i v-if='isCancel' v-on:click='isCancel=false' class="cancel">取消</i>
				</transition>
			</article>
			<article class="city-Box" v-show='!isCancel'>
				<div class="city-sidaber" id="city-sidaber" v-show='!isCancel'>
					<p v-for="(sidaber,index) in sidabers" v-on:touchstart='mousedownFun(index)' v-on:touchend='mouseupFun(index)' v-text='sidaber'></p>
				</div>
				<div class="host-city">
					<h3>热门城市</h3>
					<span v-for='(item,index) in hostCitys' v-text='item.city' v-on:click='cityactive(index,hostCitys)'></span>
				</div>
				<div class="city-content">
					<h3>A</h3>
					<p v-for='(item,index) in cityA' v-on:click='cityactive(index,cityA)'>{{item.city}}</p>
					<h3>B</h3>
					<p v-for='(item,index) in cityB' v-on:click='cityactive(index,cityB)'>{{item.city}}</p>
					<h3>C</h3>
					<p v-for='(item,index) in cityC' v-on:click='cityactive(index,cityC)'>{{item.city}}</p>
					<h3>D</h3>
					<p v-for='(item,index) in cityD' v-on:click='cityactive(index,cityD)'>{{item.city}}</p>
					<h3>E</h3>
					<p v-for='(item,index) in cityE' v-on:click='cityactive(index,cityE)'>{{item.city}}</p>
					<h3>F</h3>
					<p v-for='(item,index) in cityF' v-on:click='cityactive(index,cityF)'>{{item.city}}</p>
					<h3>G</h3>
					<p v-for='(item,index) in cityG' v-on:click='cityactive(index,cityG)'>{{item.city}}</p>
					<h3>H</h3>
					<p v-for='(item,index) in cityH' v-on:click='cityactive(index,cityH)'>{{item.city}}</p>
					<h3>J</h3>
					<p v-for='(item,index) in cityJ' v-on:click='cityactive(index,cityJ)'>{{item.city}}</p>
					<h3>K</h3>
					<p v-for='(item,index) in cityK' v-on:click='cityactive(index,cityK)'>{{item.city}}</p>
					<h3>L</h3>
					<p v-for='(item,index) in cityL' v-on:click='cityactive(index,cityL)'>{{item.city}}</p>
					<h3>M</h3>
					<p v-for='(item,index) in cityM' v-on:click='cityactive(index,cityM)'>{{item.city}}</p>
					<h3>N</h3>
					<p v-for='(item,index) in cityN' v-on:click='cityactive(index,cityN)'>{{item.city}}</p>
					<h3>P</h3>
					<p v-for='(item,index) in cityP' v-on:click='cityactive(index,cityP)'>{{item.city}}</p>
					<h3>Q</h3>
					<p v-for='(item,index) in cityQ' v-on:click='cityactive(index,cityQ)'>{{item.city}}</p>
					<h3>R</h3>
					<p v-for='(item,index) in cityR' v-on:click='cityactive(index,cityR)'>{{item.city}}</p>
					<h3>S</h3>
					<p v-for='(item,index) in cityS' v-on:click='cityactive(index,cityS)'>{{item.city}}</p>
					<h3>T</h3>
					<p v-for='(item,index) in cityT' v-on:click='cityactive(index,cityT)'>{{item.city}}</p>
					<h3>W</h3>
					<p v-for='(item,index) in cityW' v-on:click='cityactive(index,cityW)'>{{item.city}}</p>
					<h3>X</h3>
					<p v-for='(item,index) in cityX' v-on:click='cityactive(index,cityX)'>{{item.city}}</p>
					<h3>Y</h3>
					<p v-for='(item,index) in cityY' v-on:click='cityactive(index,cityY)'>{{item.city}}</p>
					<h3>Z</h3>
					<p v-for='(item,index) in cityZ' v-on:click='cityactive(index,cityZ)'>{{item.city}}</p>
				</div>
				<div v-show='zimShow' class="zimShow" v-text='zimText'></div>
			</article>
			<article class="search-box" id="search-box" v-show='isCancel'>
				<p class="sousuo" v-for='(item,index) in sousuos' v-on:click='cityactive(index,sousuos)'>{{item.city}}</p>
			</article>
		</section>
	`,
	methods:{
		//调用城市选择组件
		cityFun:function(){
	        var cityChoiceBox=document.getElementById('cityChoiceBox');
	        var citySidaber=document.getElementById('city-sidaber');
	        var clientW=document.documentElement.clientWidth||document.body.clientWidth;
	        cityChoiceBox.style.left=clientW+'px';
	        cityChoiceBox.style.display="block"
	        this.starMove(cityChoiceBox,{left:0},function(){
	        	citySidaber.style.display="block"
	        });
	     },
	     //关闭城市选择组件
	     cityClose:function(){
	     	this.isCancel=false;
	        var cityChoiceBox=document.getElementById('cityChoiceBox');
	        var citySidaber=document.getElementById('city-sidaber');
	        var clientW=document.documentElement.clientWidth||document.body.clientWidth;
	        citySidaber.style.display="none"
	        this.starMove(cityChoiceBox,{left:clientW},function(){
	        	cityChoiceBox.style.display="none"
	        });
	     },
	     //变速运动
	     starMove:function(obj,json,fn){//添加一个回调函数fn
	        function getStyle(obj,attr){
	          if(obj.currentStyle){
	            return obj.currentStyle[attr];
	            }else{
	              return getComputedStyle(obj,false)[attr];
	              }
	    	}
	        clearInterval(obj.timer);
	        obj.timer = setInterval(function(){
	          var flag = true; //假设都到达了目标值
	          for(var attr in json){
	            //1.取当前值
	            var icur = 0;
	            icur = parseInt(getStyle(obj,attr));
	            //2.算速度
	            var speed = (json[attr] - icur)/8;
	            speed = speed>0?Math.ceil(speed):Math.floor(speed);
	            //3.检查停止
	            if(icur != json[attr]){
	              flag = false;
	            }
	            obj.style[attr] = icur + speed + "px";
	            if(flag){
	              clearInterval(obj.timer);
	              if(fn){//判断是否存在回调函数,并调用
	                fn();
	                }
	              }
	            }
	        },20);
	    },
	    //搜索框获取焦点进入搜索层
	    search:function(){
	    	this.isCancel=true;
	    },
	    //热门活动
	    hostCityss:function(){
			var j=0;
			for(var i=0;i<15;i++){
               Vue.set(this.hostCitys,j,this.citys[i]);
               j++
         	}
		},
		cityactive:function(index,cityss){
			this.cityClose();
			this.zimShow=false;
			this.$emit("tochildevent",cityss[index].city)
		},
		//侧栏字母鼠标按下事件
		mousedownFun:function(index){
			this.zimShow=!this.zimShow;
			this.zimText=this.sidabers[index]
		},
		//侧栏字母鼠标弹起事件
		mouseupFun:function(index){
			this.zimShow=!this.zimShow;
			var cityChoiceBox=document.getElementById('cityChoiceBox');
			var h3=cityChoiceBox.getElementsByTagName('h3');
			var timer = null;	
			function scrT(iTarget){
				clearInterval(timer);
				document.ontouchstart=function(){
					clearInterval(timer);
				}
				timer = setInterval(function(){
				var scrollT = document.documentElement.scrollTop || document.body.scrollTop,
				    speed = 0;
				speed = Math.floor((iTarget - scrollT)/5);
				if(scrollT == iTarget){
					clearInterval(timer);
					}else{
						document.documentElement.scrollTop = scrollT + speed;
						document.body.scrollTop = scrollT + speed;
						}
				},30);
			}
			scrT(h3[index].offsetTop-50);
		}
	},
	//首字母过滤
	computed: {
        cityA: function () {
            return this.citys.filter(function (item) {
                return item.py.substr(0, 1)=='a';
            });
        },
        cityB: function () {
            return this.citys.filter(function (item) {
                return item.py.substr(0, 1)=='b';
            });
        },
        cityC: function () {
            return this.citys.filter(function (item) {
                return item.py.substr(0, 1)=='c';
            });
        },
        cityD: function () {
            return this.citys.filter(function (item) {
                return item.py.substr(0, 1)=='d';
            });
        },
        cityE: function () {
            return this.citys.filter(function (item) {
                return item.py.substr(0, 1)=='e';
            });
        },
        cityF: function () {
            return this.citys.filter(function (item) {
                return item.py.substr(0, 1)=='f';
            });
        },
        cityG: function () {
            return this.citys.filter(function (item) {
                return item.py.substr(0, 1)=='g';
            });
        },
        cityH: function () {
            return this.citys.filter(function (item) {
                return item.py.substr(0, 1)=='h';
            });
        },
        cityJ: function () {
            return this.citys.filter(function (item) {
                return item.py.substr(0, 1)=='j';
            });
        },
        cityK: function () {
            return this.citys.filter(function (item) {
                return item.py.substr(0, 1)=='k';
            });
        },
        cityL: function () {
            return this.citys.filter(function (item) {
                return item.py.substr(0, 1)=='l';
            });
        },
        cityM: function () {
            return this.citys.filter(function (item) {
                return item.py.substr(0, 1)=='m';
            });
        },
        cityN: function () {
            return this.citys.filter(function (item) {
                return item.py.substr(0, 1)=='n';
            });
        },
        cityP: function () {
            return this.citys.filter(function (item) {
                return item.py.substr(0, 1)=='p';
            });
        },
        cityQ: function () {
            return this.citys.filter(function (item) {
                return item.py.substr(0, 1)=='q';
            });
        },
        cityR: function () {
            return this.citys.filter(function (item) {
                return item.py.substr(0, 1)=='r';
            });
        },
        cityS: function () {
            return this.citys.filter(function (item) {
                return item.py.substr(0, 1)=='s';
            });
        },
        cityT: function () {
            return this.citys.filter(function (item) {
                return item.py.substr(0, 1)=='t';
            });
        },
        cityW: function () {
            return this.citys.filter(function (item) {
                return item.py.substr(0, 1)=='w';
            });
        },
        cityX: function () {
            return this.citys.filter(function (item) {
                return item.py.substr(0, 1)=='x';
            });
        },
        cityY: function () {
            return this.citys.filter(function (item) {
                return item.py.substr(0, 1)=='y';
            });
        },
        cityZ: function () {
            return this.citys.filter(function (item) {
                return item.py.substr(0, 1)=='z';
            });
        },
        //搜索过滤
        sousuos:function(){
        	var ssval = this.ssval;
        	return this.citys.filter(function (item) {
                return item.py.indexOf(ssval)!= -1 || item.city.indexOf(ssval) != -1;
            });
        }
    },
	mounted:function(){
		window.addEventListener('load',function(){
			var searchBox=document.getElementById('search-box');
			var srollH=document.documentElement.scrollHeight;
			searchBox.style.height=srollH+'px';
			
		})
		this.hostCityss();
	}

})