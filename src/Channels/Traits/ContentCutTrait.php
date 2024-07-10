<?php

namespace Kriss\Notification\Channels\Traits;

// 内容裁剪
trait ContentCutTrait
{
    /**
     * 关闭内容裁剪
     * @return bool
     */
    protected function disableContentCut(): bool
    {
        return false;
    }

    /**
     * 按指定指定字节数自动截断内容
     * 注意使用的是strlen计算占用字节数,中文是占用3个字节,emoji表情占用6个字节
     * @param string $content
     * @param int $limitLength
     * @return string
     */
    protected function cutContent(string $content, int $limitLength): string
    {
        if ($this->disableContentCut()) {
            return $content;
        }
        // 内容长度未超过限制长度
        if (strlen($content) <= $limitLength) {
            return $content;
        }
        // 将字符串按字符转换为数字开始遍历
        $contentArray = preg_split('//u', $content, -1, PREG_SPLIT_NO_EMPTY);
        $newContentStrLength = 0;// 定义一个新的字符串长度计数变量
        $newContent = '';// 新的字符串
        foreach ($contentArray as $contentChar) {
            $newContentStrLength += strlen($contentChar);// 字符串长度加上当前字符的长度
            if ($newContentStrLength > $limitLength) {
                // 如果超过限制字节跳出
                break;
            }
            // 未超过加上这个字符
            $newContent .= $contentChar;
            if ($newContentStrLength === $limitLength) {
                // 如果恰好等于限制长度字节跳出
                break;
            }
        }
        return $newContent;
    }
}