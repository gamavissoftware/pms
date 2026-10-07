let chromium;
try { chromium=require('playwright').chromium; }
catch(e) { console.log('SKIP: playwright is not installed; PHP render smoke test still verifies generated screens.'); process.exit(0); }
const fs=require('fs'),assert=require('assert');
(async()=>{
 const browser=await chromium.launch({headless:true,...(process.env.OVERTIME_TEST_CHROME ? {executablePath:process.env.OVERTIME_TEST_CHROME} : {})});
 const page=await browser.newPage({viewport:{width:1440,height:1100}});
 const errors=[];page.on('pageerror',e=>errors.push(e.message));
 const dir=(process.env.OVERTIME_PREVIEW_DIR || require('os').tmpdir()+'/pms-overtime-preview')+'/';
 for(const name of ['create','index','view','reports','settings','pending','requester']){
  await page.setContent(fs.readFileSync(dir+name+'.html','utf8'));
  assert.equal(await page.locator('h1').count(),1);
  assert.equal(await page.evaluate(()=>document.documentElement.scrollWidth>innerWidth),false,name+' desktop overflow');
  await page.screenshot({path:dir+name+'.png',fullPage:true});
  if(name==='view'){
   await page.locator('#decision').selectOption('REJECT');
   assert.equal(await page.locator('#decision-note').evaluate(el=>el.required),false,'Rejection remarks are optional');
   await page.locator('#decision').selectOption('APPROVE');
   assert.equal(await page.locator('#decision-note').evaluate(el=>el.required),false);
  }
  if(name==='create'){
   await page.locator('#ot-df').selectOption({index:1});
   await page.locator('#ot-start').fill('2026-10-01T18:00');
   await page.locator('#ot-hours').fill('2');
   await page.locator('#ot-users').selectOption({index:1});
   await page.locator('#ot-manual').fill('Labour One\nLabour Two');
   assert.match(await page.locator('#ot-duration').innerText(),/3 people .* 2.00 hours = 6.00 total person-hours/);
   await page.locator('#ot-users').selectOption([]);
   await page.locator('#ot-manual').fill('');
   await page.locator('button[type=submit]').click();assert.equal(await page.locator('#ot-loader').isVisible(),false,'Invalid form must stay editable');
   await page.locator('#ot-users').selectOption({index:1});
   await page.locator('#ot-reason').fill('Complete production packing work.');
   await page.evaluate(()=>{
    const form=document.querySelector('form');
    window.firstEvent=new Event('submit',{cancelable:true});form.dispatchEvent(window.firstEvent);
    window.secondEvent=new Event('submit',{cancelable:true});form.dispatchEvent(window.secondEvent);
   });
   assert.equal(await page.locator('#ot-loader').isVisible(),true);
   assert.equal(await page.locator('button[type=submit]').isDisabled(),true);
   assert.equal(await page.evaluate(()=>window.firstEvent.defaultPrevented),false);
   assert.equal(await page.evaluate(()=>window.secondEvent.defaultPrevented),true);
   await page.evaluate(()=>window.dispatchEvent(new Event('pageshow')));
   assert.equal(await page.locator('button[type=submit]').isDisabled(),false);
  }
  await page.setViewportSize({width:390,height:844});
  assert.equal(await page.evaluate(()=>document.documentElement.scrollWidth>innerWidth),false,name+' mobile overflow');
  await page.screenshot({path:dir+name+'-mobile.png',fullPage:true});
  await page.setViewportSize({width:1440,height:1100});
 }
 assert.deepEqual(errors,[]);await browser.close();console.log('PASS: screens and approval/cancellation variants at desktop/mobile widths, person-hour preview, validation, loading overlay, repeated submit guard, and back-navigation reset.');
})().catch(e=>{console.error(e);process.exit(1)});
