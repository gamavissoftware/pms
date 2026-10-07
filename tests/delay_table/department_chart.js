const fs = require('fs');
const vm = require('vm');
const assert = require('assert');
const source = fs.readFileSync('application/views/master/delay_table.php', 'utf8');
const start = source.indexOf('function consolidateDepartmentDelays(');
const end = source.indexOf('var departmentDetailGeneration', start);
vm.runInThisContext(source.slice(start, end));
const rows = [
 [{department_id: 1, department: 'Accounts', delay_days: 12}, {department_id: 2, department: 'Design', delay_days: 40}],
 [{department_id: 1, department: 'Accounts', delay_days: 30}, {department_id: 3, department: 'Store', delay_days: 0}]
];
assert.deepStrictEqual(consolidateDepartmentDelays(rows, ''), [{id: '2', name: 'Design', days: 40}, {id: '1', name: 'Accounts', days: 30}]);
assert.deepStrictEqual(consolidateDepartmentDelays(rows, '1'), [{id: '1', name: 'Accounts', days: 30}]);
assert.deepStrictEqual(consolidateDepartmentDelays([rows[0]], '1'), [{id: '1', name: 'Accounts', days: 12}]);
assert.deepStrictEqual(consolidateDepartmentDelays([], ''), []);
assert.deepStrictEqual(consolidateDepartmentDelays(rows, '3'), []);
assert(source.includes("table.rows({ search: 'applied', page: 'all' })"));
const scripts = [...source.replace(/<\?php[\s\S]*?\?>/g, '').matchAll(/<script(?:\s[^>]*)?>([\s\S]*?)<\/script>/g)].map(x => x[1]).join('\n');
new vm.Script(scripts);
console.log('PASS: maximum aggregation, descending order, department/DF filters, empty results, and JavaScript syntax.');

const records = rows.map((departments, index) => ({dfNo: String(1903 + index), description: 'DF', company: 'Company', departments}));
const details = departmentDfDelayRecords(records, '1');
assert.deepStrictEqual(details.map(row => [row.dfNo, row.days]), [['1904', 30], ['1903', 12]]);
assert.strictEqual(details[0].days, consolidateDepartmentDelays(rows, '1')[0].days);
assert.deepStrictEqual(departmentDfDelayRecords(records, '3'), []);
assert.strictEqual(departmentDfDelayRecords([records[0]], '1').length, 1);
assert.deepStrictEqual(departmentDfDelayRecords([], '1'), []);
console.log('PASS: DF drilldown grouping, descending delays, chart agreement, filtered and empty records.');

const users = departmentDfDelayRecords([{dfNo:'1903', departments:[{department_id:1, delay_days:12, delayed_users:['Mr. Test Owner', 'Not Assigned']}]}], '1');
assert.strictEqual(users[0].users, 'Mr. Test Owner, Not Assigned');

assert.strictEqual(departmentDfDelayRecords([{dfId: 175, dfNo: '1829', departments: [{department_id:1, delay_days:12}]}], '1')[0].dfId, 175);
assert(source.includes("task_status: 'delayed', detail_scope: 'filtered'"));
