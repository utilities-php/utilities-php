<?php

namespace Utilities\Common;

use EasyHttp\Client;

/**
 * Telegram Class
 *
 * @link    https://github.com/utilities-php/common
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/common/blob/master/LICENSE (MIT License)
 */
class Telegram
{

    /**
     * @var string
     */
    private string $token;

    /**
     * Telegram constructor.
     *
     * @param string $api_token
     */
    public function __construct(string $api_token)
    {
        $this->token = $api_token;
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function sendMessage(array $input = []): array|bool
    {
        return $this->sendRequest('sendMessage', $input);
    }

    /**
     * @param string $method
     * @param array $data
     * @return array|bool
     */
    public function sendRequest(string $method, array $data = []): array|bool
    {
        $url = "https://api.telegram.org/bot" . $this->token . "/" . $method;
        $response = (new Client())->post($url, $data);

        if ($response->getStatusCode() == 200) {
            return json_decode($response->getBody(), true);
        }

        return false;
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function forwardMessage(array $input = []): array|bool
    {
        return $this->sendRequest('forwardMessage', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function editMessageText(array $input = []): array|bool
    {
        return $this->sendRequest('editMessageText', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function leaveChat(array $input = []): array|bool
    {
        return $this->sendRequest('leaveChat', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function getChatMember(array $input = []): array|bool
    {
        return $this->sendRequest('getChatMember', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function getChatMembersCount(array $input = []): array|bool
    {
        return $this->sendRequest('getChatMembersCount', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function getChatAdministrators(array $input = []): array|bool
    {
        return $this->sendRequest('getChatAdministrators', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function getChat(array $input = []): array|bool
    {
        return $this->sendRequest('getChat', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function unpinChatMessage(array $input = []): array|bool
    {
        return $this->sendRequest('unpinChatMessage', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function pinChatMessage(array $input = []): array|bool
    {
        return $this->sendRequest('pinChatMessage', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function setChatDescription(array $input = []): array|bool
    {
        return $this->sendRequest('setChatDescription', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function setChatTitle(array $input = []): array|bool
    {
        return $this->sendRequest('setChatTitle', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function deleteChatPhoto(array $input = []): array|bool
    {
        return $this->sendRequest('deleteChatPhoto', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function setChatPhoto(array $input = []): array|bool
    {
        return $this->sendRequest('setChatPhoto', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function exportChatInviteLink(array $input = []): array|bool
    {
        return $this->sendRequest('exportChatInviteLink', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function setChatPermissions(array $input = []): array|bool
    {
        return $this->sendRequest('setChatPermissions', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function promoteChatMember(array $input = []): array|bool
    {
        return $this->sendRequest('promoteChatMember', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function restrictChatMember(array $input = []): array|bool
    {
        return $this->sendRequest('restrictChatMember', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function unbanChatMember(array $input = []): array|bool
    {
        return $this->sendRequest('unbanChatMember', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function kickChatMember(array $input = []): array|bool
    {
        return $this->sendRequest('kickChatMember', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function getFile(array $input = []): array|bool
    {
        return $this->sendRequest('getFile', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function getUserProfilePhotos(array $input = []): array|bool
    {
        return $this->sendRequest('getUserProfilePhotos', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function sendChatAction(array $input = []): array|bool
    {
        return $this->sendRequest('sendChatAction', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function sendPoll(array $input = []): array|bool
    {
        return $this->sendRequest('sendPoll', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function sendContact(array $input = []): array|bool
    {
        return $this->sendRequest('sendContact', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function sendVenue(array $input = []): array|bool
    {
        return $this->sendRequest('sendVenue', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function stopMessageLiveLocation(array $input = []): array|bool
    {
        return $this->sendRequest('stopMessageLiveLocation', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function editMessageLiveLocation(array $input = []): array|bool
    {
        return $this->sendRequest('editMessageLiveLocation', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function sendLocation(array $input = []): array|bool
    {
        return $this->sendRequest('sendLocation', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function sendMediaGroup(array $input = []): array|bool
    {
        return $this->sendRequest('sendMediaGroup', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function sendVideoNote(array $input = []): array|bool
    {
        return $this->sendRequest('sendVideoNote', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function sendVoice(array $input = []): array|bool
    {
        return $this->sendRequest('sendVoice', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function sendAnimation(array $input = []): array|bool
    {
        return $this->sendRequest('sendAnimation', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function sendVideo(array $input = []): array|bool
    {
        return $this->sendRequest('sendVideo', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function sendDocument(array $input = []): array|bool
    {
        return $this->sendRequest('sendDocument', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function sendAudio(array $input = []): array|bool
    {
        return $this->sendRequest('sendAudio', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function sendPhoto(array $input = []): array|bool
    {
        return $this->sendRequest('sendPhoto', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function setChatStickerSet(array $input = []): array|bool
    {
        return $this->sendRequest('setChatStickerSet', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function answerCallbackQuery(array $input = []): array|bool
    {
        return $this->sendRequest('answerCallbackQuery', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function setMyCommands(array $input = []): array|bool
    {
        return $this->sendRequest('setMyCommands', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function deleteMyCommands(array $input = []): array|bool
    {
        return $this->sendRequest('deleteMyCommands', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function getMyCommands(array $input = []): array|bool
    {
        return $this->sendRequest('getMyCommands', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function editMessageCaption(array $input = []): array|bool
    {
        return $this->sendRequest('editMessageCaption', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function editMessageMedia(array $input = []): array|bool
    {
        return $this->sendRequest('editMessageMedia', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function editMessageReplyMarkup(array $input = []): array|bool
    {
        return $this->sendRequest('editMessageReplyMarkup', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function stopPoll(array $input = []): array|bool
    {
        return $this->sendRequest('stopPoll', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function deleteMessage(array $input = []): array|bool
    {
        return $this->sendRequest('deleteMessage', $input);
    }

    /**
     * @param array $input
     * @return array|bool
     */
    public function getChatUsername(array $input = []): array|bool
    {
        return $this->sendRequest('getChat', $input)['result']['username'];
    }

}