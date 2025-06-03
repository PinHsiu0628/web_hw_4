import MyChart from "@/app/components/MyChart";
import {Spacer} from "@heroui/react";

export default function Average() {
    return (
        <>
            <Spacer y={8} />
            <div className={"grid place-items-center"}>
                <MyChart />
            </div>
        </>
    )
}