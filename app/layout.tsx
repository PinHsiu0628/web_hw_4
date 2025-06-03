import type { Metadata } from "next";
import "./globals.css";
import Providers from "@/app/Providers";
import {Divider, Spacer} from "@heroui/react";

export const metadata: Metadata = {
    title: "作業4 通貨膨脹-你關心缺蛋嗎?",
    description: "作業4 通貨膨脹-你關心缺蛋嗎?",
};

export default function RootLayout({
    children,
}: Readonly<{
    children: React.ReactNode;
}>) {
    return (
        <html lang="zh-TW">
            <body className={"bg-foreground text-background"}>
                <div className={"min-h-[100vh]"}>
                    <Providers>
                        {children}
                    </Providers>
                </div>
                <Spacer y={12} />
                <Divider />
                <p className={"text-center py-8 text-sm text-default-400"}>
                    © {(new Date()).getFullYear()} PinHsiu0628. All rights reserved.<br />
                    Designed by PinHsiu0628
                </p>
            </body>
        </html>
    );
}
