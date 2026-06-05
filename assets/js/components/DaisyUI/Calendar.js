import m from "mithril";

const Calendar = {
    view: ({ attrs }) => {
        return m("div.calendar", { 
            ...attrs, 
            role: "application", 
            "aria-label": "Calendar" 
        });
    }
};

export default Calendar;
