import m from "mithril";

const Hover3DCard = {
    view: ({ attrs, children }) => {
        return m(".card.bg-base-100.shadow-xl.transition-transform.duration-300.hover:scale-105.hover:rotate-1.hover:shadow-2xl", 
            { ...attrs }, 
            children
        );
    }
};

export default Hover3DCard;
