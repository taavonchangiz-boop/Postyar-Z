<?php
// Routes.php — ماژول Support
use WHCM\Core\Router;

Router::post('/dashboard/add-ticket', 'MainController@handleCreateTicket');
Router::post('/dashboard/reply-ticket', 'MainController@handleUserReplyTicket');
Router::post('/dashboard/close-ticket', 'MainController@handleCloseTicketUser');
Router::post('/hnnh/reply-ticket', 'MainController@handleReplyTicket');
Router::post('/hnnh/close-ticket', 'MainController@handleCloseTicketAdmin');
Router::post('/hnnh/assign-ticket', 'MainController@handleAssignTicket');
Router::post('/hnnh/broadcast-announcement', 'MainController@handleBroadcastAnnouncement');
