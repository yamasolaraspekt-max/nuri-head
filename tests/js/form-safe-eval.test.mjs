// FS-07 Evaluator-Regressionstest. Lauf: node tests/js/form-safe-eval.test.mjs
await import('../../public/js/form-safe-eval.js');
const E = globalThis.FormSafeEval;
let pass=0, fail=0;
const eq=(a,b,m)=>{ if(JSON.stringify(a)===JSON.stringify(b))pass++; else {fail++;console.log("FAIL",m,"=>",a,"!=",b);} };
eq(E.evalArithmetic("laenge * breite",{laenge:5,breite:4}),20,"mul");
eq(E.evalArithmetic("add(a,b,c)",{a:1,b:2,c:3}),6,"add");
eq(E.evalArithmetic("round(mul(a,b),1)",{a:2.345,b:2}),4.7,"round");
eq(E.evalArithmetic("(a+b)*2-1",{a:3,b:2}),9,"prec");
eq(E.evalArithmetic("fehlt*2",{}),0,"missing0");
eq(E.evalArithmetic("div(a,b)",{a:10,b:0}),0,"div0");
eq(E.evalArithmetic("-a+5",{a:3}),2,"unary");
eq(E.evalCondition("salary > 2000",{salary:2500}),true,"gt");
eq(E.evalCondition("salary > 2000",{salary:1500}),false,"gtf");
eq(E.evalCondition('medium == "gas"',{medium:"gas"}),true,"eqstr");
eq(E.evalCondition("a>=1 && b<10",{a:1,b:5}),true,"and");
eq(E.evalCondition("a==1 || b==2",{a:9,b:2}),true,"or");
eq(E.evalCondition("",{}),true,"empty");
eq(E.evalCondition("unbekannt == 5",{}),false,"missingvar");
// Sicherheit
eq(E.evalCondition("constructor",{}),false,"no-constructor");
eq(E.evalArithmetic("process",{}),0,"process-ist-nur-variable-kein-global");
let injected=false; globalThis.__x=()=>{injected=true;}; E.evalArithmetic("__x()",{}); eq(injected,false,"no-global-call"); delete globalThis.__x;
eq(E.evalArithmetic("alert(1)",{}),"Fehler","no-alert");
console.log("PASS="+pass+" FAIL="+fail);
process.exit(fail?1:0);
