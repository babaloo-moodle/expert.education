YUI.add("moodle-availability_payallways-form",function(e,t){M.availability_payallways=M.availability_payallways||{},M.availability_payallways.form=e.Object(M.core_availability.plugin),M.availability_payallways.form.initInner=function(e){e.cm!=null&&(this.cm=e.cm),e.access_cost!=0?this.access_cost=e.access_cost:this.access_cost=0,e.section!=null&&(this.section=e.section),this.course_id=e.course_id,this.access_options=e.options,this.headline=e.headline,this.is_section=e.is_section,console.log(this)},M.availability_payallways.form.getNode=function(t){title=M.util.get_string("title","availability_payallways"),access_cost=M.util.get_string("access_cost","availability_payallways"),html='<label class="form-group"><span class="p-r-1">'+this.headline+"</span></label>",html+='<br /><span class="availability-payallways">'+access_cost+' <input type="text" name="access_cost" /> UAH</span>';var n=e.Node.create("<span>"+html+"</span>");t.creating===undefined&&(t.access_cost!==undefined?n.one("input[name=access_cost]").set("value",t.access_cost):t.access_cost===undefined&&n.one("input[name=access_cost]").set("value","0"));if(!M.availability_payallways.form.addedEvents){M.availability_payallways.form.addedEvents=!0;var r=e.one(".availability-field");r.delegate("change",function(){M.core_availability.form.update()},".availability_payallways input")}return n},M.availability_payallways.form.fillValue=function(e,t){e.is_paid=1,e.cm=this.cm,e.access_cost=t.one("input[name=access_cost]").get("value").trim(),e.course_id=this.course_id,e.section=this.section,e.is_section=this.is_section},M.availability_payallways.form.fillErrors=function(e,t){var n={};this.fillValue(n,t),(isNaN(n.access_cost)||parseInt(n.access_cost)<=0)&&e.push("availability_payallways:cost_error")}},"@VERSION@",{requires:["base","node","event","moodle-core_availability-form"]});