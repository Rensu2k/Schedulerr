function getDaysUntil(fromDate, toDate) {
    const start = new Date(fromDate);
    start.setHours(0, 0, 0, 0);
    const target = new Date(toDate);
    target.setHours(0, 0, 0, 0);
    
    if (target <= start) return 0;

    let count = 0;
    let current = new Date(start);
    while (current < target) {
        current.setDate(current.getDate() + 1);
        const day = current.getDay();
        if (day !== 0 && day !== 6) { // 0 = Sunday, 6 = Saturday
            count++;
        }
    }
    return count;
}

// Test case: Monday Mar 16 to Monday Mar 23
const start = "2026-03-16T00:00:00";
const target = "2026-03-23T00:00:00";
console.log(`From Mar 16 to Mar 23: ${getDaysUntil(start, target)} days (Expected 5)`);

// Test case: Friday Mar 20 to Monday Mar 23
const startFri = "2026-03-20T00:00:00";
const targetMon = "2026-03-23T00:00:00";
console.log(`From Mar 20 to Mar 23: ${getDaysUntil(startFri, targetMon)} days (Expected 1: only counting Monday)`);

// Test case: Monday Mar 16 to Tuesday Mar 17
const startMon = "2026-03-16T00:00:00";
const targetTue = "2026-03-17T00:00:00";
console.log(`From Mar 16 to Mar 17: ${getDaysUntil(startMon, targetTue)} days (Expected 1)`);
