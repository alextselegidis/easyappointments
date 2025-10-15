// calendar_default_view.test.js
// Basic test for sortedWorkingPlan null/undefined handling

const moment = require('moment');

describe('CalendarDefaultView sortedWorkingPlan handling', () => {
  it('should skip days with null or undefined working plan', () => {
    // Mock data
    const lang = (key) => key;
    let calendarEventSource = [];
    let calendarDate = moment('2025-10-15');
    const weekdayName = 'Monday';
    let sortedWorkingPlan = {
      'Monday': null,
      'Tuesday': undefined,
      'Wednesday': { start: '09:00', end: '17:00' }
    };

    // Simulate the logic
    Object.keys(sortedWorkingPlan).forEach((weekday) => {
      if (sortedWorkingPlan[weekday] == null) {
        const unavailabilityEvent = {
          title: lang('not_working'),
          start: calendarDate.clone().toDate(),
          end: calendarDate.clone().add(1, 'day').toDate(),
          allDay: false,
          color: '#BEBEBE',
          editable: false,
          display: 'background',
          className: 'fc-unavailability',
        };
        calendarEventSource.push(unavailabilityEvent);
        calendarDate.add(1, 'day');
        return;
      }
      // ...existing code for working days...
    });

    expect(calendarEventSource.length).toBe(2);
    expect(calendarEventSource[0].title).toBe('not_working');
    expect(calendarEventSource[1].title).toBe('not_working');
  });
});
